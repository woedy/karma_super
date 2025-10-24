# Anti-Bot Improvement Tasklist

Track progress on the upgrades from `AGENTS.md` using this checklist. Items are grouped by priority so we harden the stack in a sane order. Tick `[x]` when a task is complete.

> **Testing cadence:** run end-to-end regression tests (bot scenarios, legitimate flows, Coolify deployment smoke tests) after finishing every priority tier before moving to the next.

## Priority 0: Immediate Containment
- [x] Rotate every leaked secret (SMTP, Telegram) and load credentials from environment variables only.
- [x] Freeze deployments that expose the current middleware until the access gate stops allowing everyone through.
- [x] Enforce HTTPS and strict sub-500 ms timeouts on all outbound lookups (ipinfo, RDAP, reverse DNS); fail fast on network errors.
- [ ] Prepare Coolify environment variables and secrets for backend and logix_frontend; ensure no credentials live in source.
- [ ] Replicate the Coolify Docker Compose layout locally and validate containers start, communicate, and shut down cleanly.

## Priority 1: Restore Baseline Safety
- [x] Replace `/api/check-access/` with real enforcement (JWT, session cookie, or signed nonce) and make the frontend respect denials.
- [ ] Clean blocklists: convert regex-style IP entries to CIDR or ASN data, drop residential ISP names, and keep the lists in config files.
- [ ] Add caching and worker refresh jobs for reputation lookups; remove synchronous per-request calls where possible.
- [ ] Encrypt or mock captured credentials and PII in lab environments; add a feature flag that disables outbound exfiltration by default.
- [ ] Wire logix_frontend to the backend via environment-driven URLs (e.g., `VITE_BACKEND_URL`) and validate requests resolve correctly inside the Coolify Docker network.
- [ ] Run end-to-end tests of the full Compose stack locally (frontend <-> backend) before redeploying through Coolify.

## Priority 2: Signal Quality and Scoring
- [ ] Collect signed client telemetry (navigator flags, timezone, canvas or WebGL hashes) and send it with each request.
- [ ] Build a scoring pipeline that blends telemetry, request rate, and reputation hints; expose thresholds for tuning.
- [ ] Implement tiered responses: soft rate limiting, proof-of-work, CAPTCHA, then hard deny.
- [ ] Log detection reasons and confidence values for every decision so students can review the trail.
- [ ] Add integration tests that replay headless, residential-proxy, and credential-stuffing scenarios through the Coolify stack.

## Priority 3: Observability and Operations
- [ ] Publish bot-mitigation metrics (allow rate, block rate, challenge success) to a dashboard.
- [ ] Forward structured events to the log stack or SIEM with correlation IDs for tracing.
- [ ] Document runbooks covering false-positive appeals, list updates, and emergency rollbacks.
- [ ] Create synthetic monitoring flows that mirror legitimate customers to detect over-blocking quickly.
- [ ] Enable Coolify health checks, container log drains, and alerting hooks for backend and frontend services.

## Priority 4: Advanced Enhancements
- [ ] Integrate curated threat feeds or commercial APIs that track bot IP ranges and automation fingerprints.
- [ ] Add machine-assisted detection (statistical models or anomaly detection) with a manual override path.
- [ ] Run tabletop scenarios for students that simulate attacks and guide mitigation exercises.
- [ ] Schedule regular audits of blocklists, telemetry signals, and secret hygiene; feed conclusions back into `AGENTS.md`.
- [ ] Automate deployment verification: scripted smoke tests post-Coolify deploy to confirm API routes, challenge responses, and frontend assets.

Keep this document current. If priorities shift or new tactics appear, update the ordering and add tasks so the roadmap always reflects the next best move.

