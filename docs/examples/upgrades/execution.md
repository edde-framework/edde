# Execution

**Related**: [Controllers](/edde/controllers)

You can execute upgrade from CLI or if you want, it's simple to implement http controller to
execute upgrades from webserver.

> Edde contains default CLI implementation for an upgrade manager.

```bash
# run upgrade up to current version available in the application
/sandbox/backend # ./cli upgrade.upgrade/upgrade

# see current version of the application
/sandbox/backend # ./cli upgrade.upgrade/version
```

?> **That's it!** Now you know how to create migrations in the application!

**Previous**: [Upgrade](/examples/upgrades/upgrade)
