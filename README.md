# TestRunsHelper Plugin

## Requirements

| Component | Version(s)                                                                                    | Link                      |
|-----------|-----------------------------------------------------------------------------------------------|---------------------------|
| PHP       | ![](https://img.shields.io/badge/7.4-blue.svg) ![](https://img.shields.io/badge/8.0-blue.svg) | [PHP](https://php.net)    |
| ILIAS     | ![](https://img.shields.io/badge/8.x-orange.svg)                                              | [ILIAS](https://ilias.de) |

Stable releases of this plugin are published in different branches of this Git repository:

* **release1_ilias8** works with ILIAS 8 
* **release2_ilias9** works with ILIAS 9

## Purpose

This plugin allows you to continue test passes that have already been completed in ILIAS. The prerequisite for this is that the number of passes is limited to 1 and a maximum processing time is set.

A toolbar button “Continue Test Passes” is then displayed on the “Participants” tab, which can be clicked if there are participants with a completed pass.

A click on the button opens a modal in which these participants can be selected individually or collectively. The modal is confirmed with “Continue Test Passes” or closed with “Cancel”.

If confirmed, the test passes of the selected participants are set to a state as if they had not been completed. To do this, their entries in the database table `tst_active` are changed:

````
tries = 0, submitted = 0, submittimestamp = NULL, last_finished_pass = NULL
````

The continuation does not automatically extend the available time. It can be changed using time extension function of ILIAS.

## Installation

1. Copy the TestRunsHelper directory to your ILIAS installation at the following path (create subdirectories, if neccessary): `Customizing/global/plugins/Services/UIComponent/UserInterfaceHook`
2. Run `composer du` in the main directory of your ILIAS installation
3. Go to Administration > Extending ILIAS > Plugins
4. Install and activate the plugin
