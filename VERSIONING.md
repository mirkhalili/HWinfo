# Versioning Policy

HWinfo uses the **QAVNS (Quadruple Adaptive Version Numbering Scheme)**.

## Identifier
`eORSN.G.FF.CCC[-PRI][-BMD]`

- **eORSN**: optional three-digit, monotonically increasing publication sequence; this project recommends it for every release and pre-release.
- **G**: generation. Increment for fundamental architectural, API, UI or structural changes; reset FF and CCC to zero.
- **FF**: feature count. Increment for a new/non-fundamental capability that does not change the API. Reset CCC to zero, or set CCC to one when the same release also contains corrections.
- **CCC**: correction count. Increment for bug fixes and cosmetic/non-content corrections when G and FF do not change.
- **PRI**: optional pre-release marker: alpha, beta or rc with a sequence number.
- **BMD**: optional build metadata such as date or build identifier.

## Git policy
1. A published tag is immutable; never move or reuse a released tag.
2. Every release gets a Git tag exactly matching the QAVNS identifier.
3. Pre-releases use `-alphaN`, `-betaN` or `-rcN`.
4. Release notes state the reason for each G/FF/CCC change.
5. Commit messages make the change class explicit: `feat:` for FF candidates, `fix:` for CCC candidates, and breaking API/UI/architecture changes for G candidates.
6. A release version is determined from the complete set of changes since the previous published version, not from one commit alone.

## Project examples
| Version | Meaning |
|---|---|
| `e001.1.00.000-alpha1` | First development baseline / alpha |
| `e002.1.01.000-beta1` | First feature addition, no correction |
| `e003.1.01.001-beta2` | Feature line plus correction |
| `e004.1.01.002` | Stable correction release |
| `e005.2.00.000-alpha1` | Fundamental generation change |

For human-facing shorthand, the core may be displayed as `G.FF.CCC`, but Git tags retain the full sortable identifier.

## Initial baseline
The repository starts from Git initial commit. The first documented application baseline is planned as `e001.1.00.000-alpha1` after the initial project structure is committed and reviewed.
