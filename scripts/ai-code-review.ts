// Note: Reviews are limited to the first 20,000 characters of diff for efficiency.
import { Octokit } from "@octokit/rest";
import { info, warning, setFailed } from "@actions/core";
import * as github from "@actions/github";

const REQUIRED_ENV_VARS = ['GITHUB_TOKEN', 'ANTHROPIC_API_KEY'];
for (const envVar of REQUIRED_ENV_VARS) {
    if (!process.env[envVar]) {
        setFailed(`${envVar} is required but not set`);
        process.exit(1);
    }
}

if (diffs.length === 0) {
    info("No code changes to review (binary files only?)");
    process.exit(0);
}

const MAX_DIFF_CHARS = 20000;
const MAX_RESPONSE_TOKENS = 2500;
const ANTHROPIC_API_VERSION = "2023-06-01";
const ANTHROPIC_MODEL = process.env.ANTHROPIC_MODEL || 'claude-sonnet-4.1';

const octokit = new Octokit({ auth: process.env.GITHUB_TOKEN });
const { owner, repo } = github.context.repo;
const prNumber = github.context.payload.pull_request?.number;

if (!owner || !repo || !prNumber) {
    setFailed("Missing PR context (owner/repo/number)");
    process.exit(1);
}

async function main() {
    info(`Fetching PR #${prNumber} from ${owner}/${repo}`);

    const files = await octokit.paginate(octokit.rest.pulls.listFiles, {
        owner,
        repo,
        pull_number: prNumber,
        per_page: 100,
    });


    const diffs = files
        .map(f => f.patch)
        .filter(Boolean)
        .join("\n\n");

    if (!diffs.trim()) {
        info("No text diffs found (possibly all binary files). Skipping AI review.");
        return;
    }

    if (diffs.length > MAX_DIFF_CHARS) {
        warning(`Diff truncated from ${diffs.length} to ${MAX_DIFF_CHARS} chars`);
    }

    const truncatedDiff = diffs.slice(0, MAX_DIFF_CHARS);
    info(`Sending ${truncatedDiff.length} characters of diff to Anthropic...`);

    const prompt = `
You are an expert software engineer performing a code review.
Provide specific, concise feedback on code quality, security, maintainability, and style.
Be objective and constructive.

--- DIFF START ---
${truncatedDiff}
--- DIFF END ---
`;

    if (!process.env.ANTHROPIC_API_KEY) {
        setFailed("ANTHROPIC_API_KEY is not set");
        process.exit(1);
    }

    const res = await fetch("https://api.anthropic.com/v1/messages", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "x-api-key": process.env.ANTHROPIC_API_KEY,
            "anthropic-version": ANTHROPIC_API_VERSION,
        },
        body: JSON.stringify({
            model: ANTHROPIC_MODEL,
            max_tokens: MAX_RESPONSE_TOKENS,
            messages: [{ role: "user", content: prompt }],
        }),
    });

    if (!res.ok) {
        const raw = await res.text();
        throw new Error(`Anthropic API error: ${res.status} ${raw.slice(0, 200)}`);
    }

    const response = await res.json();
    function extractReview(resp: any): string {
        if (!resp || !resp.content || !Array.isArray(resp.content)) return "⚠️ No feedback received.";
        const first = resp.content[0];
        if (!first || typeof first.text !== "string") return "⚠️ No feedback received.";
        return first.text;
    }

    const review = extractReview(response);

    info("Posting review comment to PR...");

    await octokit.rest.issues.createComment({
        owner,
        repo,
        issue_number: Number(prNumber),
        body: `🤖 **AI Code Review by Claude**\n\n${review}`,
    });

    info("✅ Review posted successfully!");
}

main().catch(err => {
    setFailed(`AI Code Review failed: ${err.message}`);
});
