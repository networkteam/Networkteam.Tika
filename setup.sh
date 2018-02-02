#!/bin/bash
mkdir -p Build/repository/tika
for file in `find .  -depth 1 -name "*" -not -path "./Build*" -not -path "."`;
do
  cp -r $file Build/repository/tika/
done
cd Build
cat <<EOF > composer.json
{
    "name": "typo3/flow-base-distribution",
    "config": {
        "vendor-dir": "Packages/Libraries",
        "bin-dir": "bin"
    },
    "repositories": [
      {
        "type": "path",
        "url": "repository/tika"
      }
    ],
    "require": {
        "networkteam/tika": "dev-master"
    },
    "require-dev": {
        "neos/buildessentials": "dev-master",
        "mikey179/vfsstream": "1.1.*"
    },
    "minimum-stability": "dev",
    "scripts": {
        "post-update-cmd": "Neos\\\\Flow\\\\Composer\\\\InstallerScripts::postUpdateAndInstall",
        "post-install-cmd": "Neos\\\\Flow\\\\Composer\\\\InstallerScripts::postUpdateAndInstall"
    }
}
EOF

composer require neos/flow:${FLOW_VERSION} --no-update
composer require phpunit/phpunit:${PHPUNIT_VERSION} --no-update
composer update
