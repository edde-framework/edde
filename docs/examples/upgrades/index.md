# Upgrades

**Related**: [Upgrades](/edde/upgrades), [Configurators](/edde/configurators)

This is tutorial how to implement Upgrade Manager (migration support). 

Edde does not provide Upgrade Manager by default as there is just abstract implementation
for easy integration.

Use `AbstractUpgradeManager` as the base class for you implementation, bind it against `IUpgradeManager`
interface and you are happy!

?> Code here is basically ready to use, so you can copy-paste-run it only with little adjustments.

* [Schema](/examples/upgrades/schema): How to create a schema for Upgrades.
* [Upgrade Manager](/examples/upgrades/upgrade-manager): Sample implementation of an Upgrade Manager.
* [Configurator](/examples/upgrades/configurator): Way, how to configure an Upgrade Manager.
* [Registration](/examples/upgrades/registration): Connect things together.
* [Upgrade](/examples/upgrades/upgrade): Sample Upgrade class.
* [Execution](/examples/upgrades/execution): How to run the thing.
