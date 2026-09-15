# Changelog

## 1.1 - 2026-09-15

- user-facing documentation aligned with the current Symcon product naming
- reference mapping updated for MySkoda 1.1
- `PendingCommands` and `CommandStatus` added to the supported MySkoda data points
- diagnostic object group renamed to **API** for new installations
- existing managed default group **Diagnose/Diagnostics** is migrated to **API** while preserving user-renamed groups
- supported MySkoda reference model increased from 30 to 32 data points

## 1.0 - 2026-09-06

- Initial release of EV Tile for Symcon
- manufacturer-independent native Symcon object architecture with MySkoda as the first fully supported data source
- automatic mapping by exact technical variable Ident with variable type validation
- compact assignment status with automatically detected, manually assigned and missing data points
- manual per-data-point fallback mapping
- optional grouped vehicle view with native group instances and links to the original vehicle variables
- Vehicle group as the compact overview for the Tile Visualization
- links keep the original variable name, icon, presentation and actions
- optional native charging chart with state of charge and charging limit on the left axis and charging power on the right axis
- no HTML visualization, no mirrored vehicle variables and no external network requests
