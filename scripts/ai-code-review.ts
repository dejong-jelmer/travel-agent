import { Octokit } from "@octokit/rest";
import { info, warning, setFailed } from "@actions/core";
import * as github from "@actions/github";

const MAX_DIFF_CHARS = 20000;
const MAX_RESPONSE_TOKENS = 2500;
const ANTHROPIC_API_VERSION = "2023-06-01";
const ANTHROPIC_MODEL = "claude-sonnet-4-5-20250929";

const octokit = new Octokit({ auth: process.env.GITHUB_TOKEN });
const { owner, repo } = github.context.repo;
const prNumber = github.context.payload.pull_request?.number;

if (!owner || !repo || !prNumber) {
  setFailed("Missing PR context (owner/repo/number)");
  process.exit(1);
}

async function main() {
  info(`Fetching PR #${prNumber} from ${owner}/${repo}`);

  const { data: files } = await octokit.rest.pulls.listFiles({
    owner,
    repo,
    pull_number: Number(prNumber),
    per_page: 100,
  });

  const diffs = files
    .map(f => f.patch)
    .filter(Boolean)
    .join("\n\n");

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

  const res = await fetch("https://api.anthropic.com/v1/messages", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
      "x-api-key": process.env.ANTHROPIC_API_KEY!,
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
  const review = response.content?.[0]?.text || "⚠️ No feedback received.";

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
