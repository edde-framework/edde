# Versioning

Because SemVer has some flaws mainly related to hiding weight of API change when even a little change could 
bump major version same as fully rewritten library, Edde **do not** follow this concept.

## Major version

Is changed when there is a **new generation** of Edde or some huge part of framework has been changed and there
is a lot of work required to update to a new version. That's because it's major version, not tiny a bit version
bump, but major change in codebase.

## Minor version

Minor bumps are meant to track smaller, usually incompatible changes, thus `5.1` and `5.2` will not probably
work after upgrade, but it should be much more easier to do the upgrade than in major version.

## Patch level

This is the only compatible version change, usually not used. Even bug fix could be API breaking change.
