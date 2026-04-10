# WATCHLIST

Implementation roadmap for improving LibVapmPMMP performance, developer experience, and PocketMine-MP compatibility.

## Priority legend
- **P0**: critical impact, should be addressed first
- **P1**: high value, planned after P0
- **P2**: useful improvements, can be scheduled later

## Queue and throughput
| ID | Priority | Improvement | Code surface | Why |
|---|---|---|---|---|
| Q-01 | P0 | Replace `EventLoop::getQueue()` linear scan with O(1) ID index + safe cleanup on dequeue/resolve. | `src\vennv\vapm\EventLoop.php` | Reduces lookup pressure under very large queue counts. |
| Q-02 | P0 | Add duplicate-enqueue protection for Promise IDs in the event queue. | `EventLoop`, `Promise` | Prevents queue growth from repeated enqueue of same promise. |
| Q-03 | P1 | Add tunable scheduler limits (base/max per run, GC sweep size/interval). | `Settings`, `EventLoop`, `MacroTask`, `CoroutineGen` | Lets low-end servers trade throughput for stability. |
| Q-04 | P1 | Add backlog-aware fairness between Promise queue, MacroTask, and Coroutine queues. | `EventLoop`, `MacroTask`, `CoroutineGen` | Prevents starvation and smooths tick latency. |
| Q-05 | P2 | Add optional chunked draining strategy for `Work::run()` and large producer loops. | `Work`, `Worker` | Improves memory behavior during bursty workloads. |

## PocketMine-MP compatibility and safety
| ID | Priority | Improvement | Code surface | Why |
|---|---|---|---|---|
| C-01 | P0 | Add explicit PMMP lifecycle guardrails (init once, scheduler state checks, shutdown safety). | `VapmPMMP`, `System` | Avoids accidental double init and runtime edge cases. |
| C-02 | P1 | Add compatibility notes and tests for API 5 behavior assumptions around ticks and shutdown callbacks. | `System`, docs | Makes behavior predictable across PMMP updates. |
| C-03 | P2 | Add fallback/no-op behavior for unsupported contexts with clear developer-facing error messages. | `System`, `Error` | Improves integration experience for plugin developers. |

## Developer ergonomics
| ID | Priority | Improvement | Code surface | Why |
|---|---|---|---|---|
| D-01 | P1 | Add quick-start recipes for common patterns (timeouts, intervals, fetch, parallel awaits). | `README.md` | Reduces onboarding friction. |
| D-02 | P1 | Add examples for high-volume queue scenarios and backpressure-friendly usage. | `README.md`, examples | Helps developers avoid anti-patterns. |
| D-03 | P2 | Add clearer type docs for Promise/Async/Thread inputs and return flow. | phpdoc across `Promise`, `Thread`, `Work` | Lowers misuse and static-analysis noise. |

## Observability and diagnostics
| ID | Priority | Improvement | Code surface | Why |
|---|---|---|---|---|
| O-01 | P0 | Add lightweight metrics snapshot API (queue depth, processed count, drops, coroutine backlog). | `EventLoop`, `CoroutineGen`, `MicroTask`, `MacroTask` | Enables real performance tuning and regression detection. |
| O-02 | P1 | Add optional debug mode for per-tick scheduler stats. | `System`, `Settings` | Helps diagnose low-end server bottlenecks quickly. |
| O-03 | P2 | Add periodic health warnings when backlog or drops exceed thresholds. | `System`, `Settings` | Improves operational visibility in production servers. |

## Reliability and quality gates
| ID | Priority | Improvement | Code surface | Why |
|---|---|---|---|---|
| R-01 | P0 | Add deterministic stress scripts for large queue loads and mixed task types. | test/benchmark scripts | Prevents regressions in throughput changes. |
| R-02 | P1 | Add static-analysis checks for queue/typing invariants around scheduler internals. | `phpstan.neon.dist`, core classes | Keeps performance refactors safe. |
| R-03 | P2 | Add comparative benchmark notes (before/after) for key scheduler changes. | docs | Guides future optimization decisions. |

## Suggested execution order
1. Q-01, Q-02, O-01
2. C-01, Q-03, R-01
3. Q-04, D-01, D-02
4. O-02, C-02, R-02
5. Remaining P2 items

## Definition of done for each item
- Change is linked to concrete files/classes.
- Behavior remains compatible with PMMP API 5 and supported PHP versions.
- Static analysis passes.
- Where relevant, measurable queue/tick metrics show no regression.
