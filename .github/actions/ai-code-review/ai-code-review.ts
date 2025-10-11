// Note: Reviews are limited to the first 20,000 characters of diff for efficiency.
import { Octokit } from "@octokit/rest";
import { info, warning, setFailed } from "@actions/core";
import * as github from "@actions/github";

// Constants
const MAX_DIFF_CHARS = 20000;
const MAX_RESPONSE_TOKENS = 2500;
const API_TIMEOUT_MS = 60000; // 60 seconds
const MAX_RETRIES = 3;
const RETRY_DELAY_MS = 1000;
const TOKEN_ESTIMATE_DIVISOR = 4;
const ANTHROPIC_API_URL = "https://api.anthropic.com/v1/messages";
const ANTHROPIC_API_VERSION = process.env.ANTHROPIC_API_VERSION || "2023-06-01";
const ANTHROPIC_MODEL = process.env.ANTHROPIC_MODEL || "claude-sonnet-4-5-20250929";
const CUSTOM_PROMPT = process.env.CUSTOM_PROMPT || `You are an expert software engineer performing a code review.
Provide specific, concise feedback on code quality, security, maintainability, and style.
Be objective and constructive.`;

interface AnthropicTextContent {
    type: "text";
    text: string;
}

interface AnthropicResponse {
    content?: AnthropicTextContent[];
}

interface FileWithDiff {
    filename: string;
    patch?: string;
}

interface Config {
    octokit: Octokit;
    owner: string;
    repo: string;
    prNumber: number;
    apiKey: string;
}

function log(message: string) {
    const timestamp = new Date().toISOString();
    info(`[${timestamp}] ${message}`);
}

function validateEnvironment(): void {
    const requiredVars = ["GITHUB_TOKEN", "ANTHROPIC_API_KEY"];
    const missing = requiredVars.filter(v => !process.env[v]);

    if (missing.length > 0) {
        throw new Error(`Required environment variables not set: ${missing.join(", ")}`);
    }
}

function initializeConfig(): Config {
    validateEnvironment();

    const { owner, repo } = github.context.repo;
    const prNumber = github.context.payload.pull_request?.number;

    if (!owner || !repo || typeof prNumber !== "number") {
        throw new Error("Missing or invalid PR context (owner/repo/number)");
    }

    const apiKey = process.env.ANTHROPIC_API_KEY!;
    const octokit = new Octokit({ auth: process.env.GITHUB_TOKEN });

    return { octokit, owner, repo, prNumber, apiKey };
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

async function callAnthropicWithRetry(prompt: string, apiKey: string): Promise<AnthropicResponse> {
    let lastError: Error | null = null;

    for (let attempt = 1; attempt <= MAX_RETRIES; attempt++) {
        try {
            log(`Anthropic API call attempt ${attempt}/${MAX_RETRIES}`);

            const res = await fetchWithTimeout(
                ANTHROPIC_API_URL,
                {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "x-api-key": apiKey,
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
                const retryAfterSecs = retryAfter ? parseInt(retryAfter, 10) : null;
                const delay = (retryAfterSecs && !isNaN(retryAfterSecs))
                    ? retryAfterSecs * 1000
                    : RETRY_DELAY_MS * Math.pow(2, attempt - 1);
                warning(`Rate limited (429). Retrying after ${delay}ms...`);
                await sleep(delay);
                continue;
            }

            if (!res.ok) {
                const errorText = await res.text();
                throw new Error(`Anthropic API error: ${res.status} - ${errorText.slice(0, 200)}`);
            }

            return await res.json();
        } catch (err) {
            lastError = err instanceof Error ? err : new Error(String(err));

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

function truncateAtFileBoundary(
    files: FileWithDiff[],
    maxChars: number
): { diff: string; truncated: boolean; filesIncluded: number } {
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
    const config = initializeConfig();
    const { octokit, owner, repo, prNumber, apiKey } = config;

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

    if (filesWithPatches.length > 100) {
        warning(`Large PR detected: ${filesWithPatches.length} files. Review may take longer.`);
    }

    const { diff, truncated, filesIncluded } = truncateAtFileBoundary(filesWithPatches, MAX_DIFF_CHARS);

    if (!diff || diff.trim().length === 0) {
        warning("No diff content to review after truncation");
        return;
    }

    if (truncated) {
        warning(`Diff truncated: showing ${filesIncluded} of ${filesWithPatches.length} files (${diff.length} chars)`);
    }

    log(`Sending ${diff.length} characters of diff to Anthropic (model: ${ANTHROPIC_MODEL})...`);

    const prompt = `${CUSTOM_PROMPT} --- DIFF START --- ${diff} --- DIFF END ---`;

    const response = await callAnthropicWithRetry(prompt, apiKey);
    const review = extractReview(response);

    const estimatedInputTokens = Math.ceil(diff.length / TOKEN_ESTIMATE_DIVISOR);
    log(`Review completed. Estimated tokens used: ~${estimatedInputTokens} input + ${MAX_RESPONSE_TOKENS} output (max)`);

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
