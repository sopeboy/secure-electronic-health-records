# Contributing to SEHRS

## Branches
- `main` is protected. Nobody pushes to it directly.
- One branch per task: `feature/<name>`, `fix/<name>`, `test/<name>`, `docs/<name>`, `chore/<name>`.

## Commits
Small and frequent, using these prefixes:
- `feat:` new feature — `feat: add patient registration form`
- `fix:` bug fix — `fix: block suspended users at login`
- `test:` tests — `test: nurse cannot open admin page`
- `docs:` documentation — `docs: add STRIDE threat model`
- `chore:` setup/config — `chore: configure MySQL in .env.example`

## Pull requests
1. Push your branch and open a pull request into `main`.
2. Fill in the pull request template.
3. At least one other member reviews and approves before merging.
4. GitHub Actions tests must pass (from Week 5).

## Never commit
`.env`, passwords, API tokens, keys or any real patient data.
Use `.env.example` with placeholders and the seeders for fake data.
