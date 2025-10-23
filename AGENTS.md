# Anti-Bot Strategy Playbook

This playbook lists the improvements we will pursue so the anti-bot stack behaves like a production-ready control plane. Refer to it whenever we adjust tactics or explain the roadmap to students.

## 1. Threat Landscape Snapshot
- Commodity crawlers: search and scraper bots with obvious user agents or well-known network ranges.
- Headless browsers: Puppeteer and Selenium drivers that mimic modern browsers while rotating residential proxies.
- API scrapers and credential stuffing: low-and-slow traffic blending in with human timing while replaying stolen credentials.
- Adaptive adversaries: attackers that iterate quickly to bypass static rules, replay challenges, and weaponize machine learning.

## 2. Current Controls (audit date: 2025-10)
- Static allow and deny lists for IPs, ASNs, ISP names, and user-agent keywords.
- Middleware performs synchronous ipinfo, RDAP, and reverse DNS lookups on each request, creating latency and DoS risk.
- Frontend gate is cosmetic: `/api/check-access/` always returns 200 and the client defaults to allow on any error.
- Secrets such as SMTP and Telegram credentials are bundled into the frontend with no rotation or storage hygiene.
- Captured credentials and PII leave the system through email and Telegram without encryption or audit controls.

## 3. Deployment Context
- Target platform: private Ubuntu server managed through Coolify (Docker Compose). Aim for the simplest possible configuration: one backend service, one logix_frontend service, both driven purely by `.env` values in the Coolify dashboard. Mirror the same Docker Compose stack locally so we can rehearse everything (bot tests, legitimate flows, deployments) before shipping to the server.
- Backend: run the Django container behind Coolify's built-in reverse proxy with Let's Encrypt; rely on default health checks and avoid custom reverse-proxy tweaks unless strictly necessary.
- Frontend (logix_frontend): deploy the Vite build using Coolify's static-site or Node service template; set `VITE_BACKEND_URL` to the backend service URL via environment variables and avoid extra networking layers.
- Shared configuration: keep a single `.env.example` per service, minimize volume mounts, and let Coolify manage secrets and restarts automatically.
- Monitoring hooks: stick with Coolify's default container status dashboard and simple log streaming so the bootcamp team can verify health at a glance.

## 3. Tactical Improvement Pillars

### 3.1 Signal quality and coverage
- Store indicators as structured data: convert regex IP ranges to CIDR blocks, maintain ASNs with automated feeds, and treat hits as confidence boosts instead of automatic blocks.
- Collect signed browser telemetry (navigator flags, WebGL hashes, timezone, automation markers) so the backend can score requests beyond IP and UA.
- Track behavioral metrics (request velocity, funnel completion, failure ratios) and trigger scoring when anomalies appear.

### 3.2 Challenge and remediation
- Layer defensive responses: start with rate limiting, escalate to proof-of-work or CAPTCHA, and fall back to out-of-band verification before final denial.
- Offer recovery paths for high-value sessions so legitimate users can regain access quickly.
- Keep a challenge ledger with timestamps, scores, and outcomes to audit false positives and retrain rules.

### 3.3 Performance and resilience
- Move reputation lookups into asynchronous workers with caching; wrap every outbound call in HTTPS and strict sub-500 ms timeouts.
- Precompute RDAP and ASN data on a refresh schedule instead of per request.
- Add circuit breakers so rule failures fall back to behavior scoring rather than locking out users.

### 3.4 Secret and data hygiene
- Load all integration keys from environment variables or a secrets manager; rotate anything already exposed.
- Encrypt stored payloads, gate access behind roles, and scrub data when running classroom demos.
- Attach correlation IDs to logs while redacting sensitive fields, and expose audit trails to instructors.

### 3.5 Observability and feedback
- Export allow rate, block rate, challenge success, and false positive counts to dashboards such as Grafana.
- Forward structured detection events to the log stack or SIEM for replay and tuning.
- Maintain synthetic "canary" sessions that represent legitimate users to detect regressions quickly.
- Feed container health and latency metrics into Coolify's dashboard or an external collector to verify the deployment layer remains healthy during stress tests.

### 3.6 Governance and process
- Document runbooks for tuning thresholds, approving allowlists, and triaging user reports.
- Require peer review for blocklist edits and record the change history in git.
- Design tabletop exercises for the bootcamp where students respond to staged attacks using the improved controls.
- Enforce a "test-as-we-go" cadence: complete and verify every priority tier (see `IMPROVEMENTS.md`) with full scenario testing before graduating to the next tranche.

## 4. Roadmap Themes
1. Stabilize foundations: rotate secrets, fix the access gate, and reformat blocklists.
2. Enrich signals: combine client telemetry, behavioral analytics, and curated threat intelligence.
3. Adaptive defense: add layered challenges, human-in-the-loop review, and optional machine scoring.
4. Operational maturity: build monitoring, automated regression tests, and documentation so students see real-world practices.

## 5. Demo Guidance
- Run the system in a sandbox but enforce production norms such as HTTPS termination and secret segregation.
- Simulate multiple attacker profiles (simple crawler, headless Chrome, distributed credential stuffing) and observe the layered response.
- Demonstrate false-positive handling by walking through a legitimate customer scenario and the remediation workflow.
- Validate end-to-end behavior from logix_frontend through the backend within the Coolify stack before each bootcamp run, including API routing, WebSocket channels (if any), and challenge/response flows.

Update this file whenever new intelligence, patterns, or defensive capabilities emerge so the bootcamp mirrors active security operations.


