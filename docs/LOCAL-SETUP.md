# Local revival — 2026-09-29

This checkout is `/Users/tac/sites/feeds`, served at https://feeds.wip.
It preserves the existing Tabler theme through survos/bootstrap-bundle and Auth.
The source repository main was Symfony 7.3.1; the runnable baseline is now Symfony
7.4.19 on PHP 8.5.10. Symfony 8.2-dev and current Survos bundles are a subsequent
modernization step, not part of this baseline.

Removed old `/home/tac/g/...` path dependencies; Graby and Readability now resolve
from published fork sources. Updated Flex, Symfony, Twig and PHP-constrained
libraries. Composer audit reports no security advisories. Doctrine excludes shared
Postgres extension schemas from management.

Local `.env.local` uses database `feeds` on shared PostgreSQL port 5434, the `feeds`
RabbitMQ vhost, and a fresh app secret. Test DB override points to `feeds_test`.
Credentials are local only. No recurring scraper or permanent worker was started.

Start: `symfony server:start -d`. Queue: `php bin/console messenger:consume fetch_items`.
Verified homepage, dashboard, feeds and new-feed pages; admin login; container,
YAML and Twig lint; Doctrine mapping/schema; RabbitMQ connection/worker startup.
The inherited test suite still targets PHPUnit 8.5 and has not been modernized or
run as part of this installation.

Repository history explains the split: f43 started Tabler on 2024-11-15 immediately
before feeds was created. Feeds moved to Symfony 7.3 on 2025-11-23. Subsequent
Symfony 8 (2026-04-08) and 8.1 (2026-08-06) work landed in the separate f43 fork.
Use feeds as the new development base and preserve f43's relevant fixes as reference.
