// Note: Reviews are limited to the first 20,000 characters of diff for efficiency.
import { Octokit } from "@octokit/rest";
import { info, warning, setFailed } from "@actions/core";
import * as github from "@actions/github";

const REQUIRED_ENV_VARS = ["GITHUB_TOKEN", "ANTHROPIC_API_KEY"];
for (const envVar of REQUIRED_ENV_VARS) {
    if (!process.env[envVar]) {
        setFailed(`${envVar} is required but not set`);
    }
}

const MAX_DIFF_CHARS = 20000;
const MAX_RESPONSE_TOKENS = 2500;
const ANTHROPIC_API_VERSION = process.env.ANTHROPIC_API_VERSION || "2023-06-01";
const ANTHROPIC_MODEL = process.env.ANTHROPIC_MODEL || "claude-sonnet-4-5-20250929";
const PROMPT = process.env.PROMPT;
const API_KEY = process.env.ANTHROPIC_API_KEY!;
const API_TIMEOUT_MS = 60000; // 60 seconds
const MAX_RETRIES = 3;
const RETRY_DELAY_MS = 1000;

const octokit = new Octokit({ auth: process.env.GITHUB_TOKEN });
const { owner, repo } = github.context.repo;
const prNumber = github.context.payload.pull_request?.number;

if (!owner || !repo || !prNumber || typeof prNumber !== "number") {
    setFailed("Missing or invalid PR context (owner/repo/number)");
}

interface AnthropicTextContent {
    type: "text";
    text: string;
}

interface AnthropicResponse {
    content?: AnthropicTextContent[];
}

function log(message: string) {
    const timestamp = new Date().toISOString();
    info(`[${timestamp}] ${message}`);
}

function extractReview(resp: AnthropicResponse): string {
    if (!resp?.content || !Array.isArray(resp.content)) {
        return "⚠️ No feedback received.";
    }
    const first = resp.content[0];
    if (!first || first.type !== "text" || !first.text) {
        return "⚠️ No feedback received.";
    }
    return first.text;
}

async function fetchWithTimeout(url: string, options: RequestInit, timeoutMs: number): Promise<Response> {
    const controller = new AbortController();
    const timeout = setTimeout(() => controller.abort(), timeoutMs);

    try {
        const response = await fetch(url, {
            ...options,
            signal: controller.signal,
        });
        return response;
    } finally {
        clearTimeout(timeout);
    }
}

async function sleep(ms: number): Promise<void> {
    return new Promise(resolve => setTimeout(resolve, ms));
}

async function callAnthropicWithRetry(prompt: string): Promise<AnthropicResponse> {
    let lastError: Error | null = null;

    for (let attempt = 1; attempt <= MAX_RETRIES; attempt++) {
        try {
            log(`Anthropic API call attempt ${attempt}/${MAX_RETRIES}`);

            const res = await fetchWithTimeout(
                "https://api.anthropic.com/v1/messages",
                {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "x-api-key": API_KEY,
                        "anthropic-version": ANTHROPIC_API_VERSION,
                    },
                    body: JSON.stringify({
                        model: ANTHROPIC_MODEL,
                        max_tokens: MAX_RESPONSE_TOKENS,
                        messages: [{ role: "user", content: prompt }],
                    }),
                },
                API_TIMEOUT_MS
            );

            if (res.status === 429) {
                const retryAfter = res.headers.get("retry-after");
                const delay = retryAfter ? parseInt(retryAfter) * 1000 : RETRY_DELAY_MS * Math.pow(2, attempt - 1);
                warning(`Rate limited (429). Retrying after ${delay}ms...`);
                await sleep(delay);
                continue;
            }

            if (!res.ok) {
                throw new Error(`Anthropic API error: ${res.status}`);
            }

            return await res.json();
        } catch (err) {
            lastError = err as Error;

            if (err instanceof Error && err.name === "AbortError") {
                warning(`Request timeout on attempt ${attempt}`);
            } else {
                warning(`API call failed on attempt ${attempt}: ${err}`);
            }

            if (attempt < MAX_RETRIES) {
                const delay = RETRY_DELAY_MS * Math.pow(2, attempt - 1);
                log(`Retrying in ${delay}ms...`);
                await sleep(delay);
            }
        }
    }

    throw new Error(`Failed after ${MAX_RETRIES} attempts. Last error: ${lastError?.message}`);
}

interface FileWithDiff {
    filename: string;
    patch?: string;
}

function truncateAtFileBoundary(files: FileWithDiff[], maxChars: number): { diff: string; truncated: boolean; filesIncluded: number } {
    let totalLength = 0;
    let filesIncluded = 0;
    const includedDiffs: string[] = [];

    for (const file of files) {
        if (!file.patch) continue;

        const fileHeader = `\n--- ${file.filename} ---\n`;
        const fileDiff = fileHeader + file.patch;
        const newLength = totalLength + fileDiff.length;

        if (newLength > maxChars && filesIncluded > 0) {
            break;
        }

        includedDiffs.push(fileDiff);
        totalLength = newLength;
        filesIncluded++;

        if (totalLength >= maxChars) {
            break;
        }
    }

    return {
        diff: includedDiffs.join("\n"),
        truncated: filesIncluded < files.filter(f => f.patch).length,
        filesIncluded
    };
}

async function main() {
    log(`Fetching PR #${prNumber} from ${owner}/${repo}`);

    const files = await octokit.paginate(octokit.rest.pulls.listFiles, {
        owner,
        repo,
        pull_number: prNumber,
        per_page: 100,
    });

    const filesWithPatches = files.filter(f => f.patch);

    log(`Found ${files.length} files in PR (${filesWithPatches.length} with diffs)`);

    if (filesWithPatches.length === 0) {
        warning("No text diffs found (possibly all binary files). Skipping AI review.");
        return;
    }

    const { diff, truncated, filesIncluded } = truncateAtFileBoundary(filesWithPatches, MAX_DIFF_CHARS);

    if (truncated) {
        warning(`Diff truncated: showing ${filesIncluded} of ${filesWithPatches.length} files (${diff.length} chars)`);
    }

    log(`Sending ${diff.length} characters of diff to Anthropic (model: ${ANTHROPIC_MODEL})...`);

    const prompt =
        PROMPT +
        `
--- DIFF START ---
${diff}
--- DIFF END ---
`;

    const response = await callAnthropicWithRetry(prompt);
    const review = extractReview(response);

    log(`Review completed. Estimated tokens used: ~${Math.ceil(diff.length / 4)} input + ${MAX_RESPONSE_TOKENS} output (max)`);

    log("Posting review comment to PR...");

    const truncationNote = truncated
        ? `\n\n> ⚠️ **Note**: This review covers ${filesIncluded} of ${filesWithPatches.length} changed files due to size limits.\n`
        : "";

    await octokit.rest.issues.createComment({
        owner,
        repo,
        issue_number: prNumber,
        body: `🤖 **AI Code Review by Claude**${truncationNote}\n${review}`,
    });

    log("✅ Review posted successfully!");
}

main().catch(err => {
    setFailed(`AI Code Review failed: ${err.message}`);
});
