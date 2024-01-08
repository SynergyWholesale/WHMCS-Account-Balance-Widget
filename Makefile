SW_API_HOSTNAME ?= api.synergywholesale.com
SW_FRONTEND_HOSTNAME ?= manage.synergywholesale.com
RELEASE_DATE := $(shell date '+%A, %B %d %Y')

# Make sure sed replace works on Mac OSX
SED_PARAM := 
ifeq ($(shell uname -s),Darwin)
	SED_PARAM += ''
endif

# In case the version tag isn't annoated, let's have a fallback
VERSION := $(shell git describe --abbrev=0)
ifneq ($(.SHELLSTATUS), 0)
	VERSION := $(shell git describe --tags)
endif

VERSION := $(firstword $(subst -, ,${VERSION}))

replace:
	sed -i${SED_PARAM} "s/{{VERSION}}/${VERSION}/g" "README.txt"
	sed -i${SED_PARAM} "s/{{RELEASE_DATE}}/${RELEASE_DATE}/g" "README.txt"
	sed -i${SED_PARAM} "s/{{VERSION}}/${VERSION:v%=%}/g" "modules/addons/synergywholesale_balance/synergywholesale_balance.php"
	sed -i${SED_PARAM} "s/{{VERSION}}/${VERSION:v%=%}/g" "modules/widgets/synergywholesale_balance.php"
	sed -i${SED_PARAM} "s/{{API}}/${SW_API_HOSTNAME}/g" "modules/widgets/synergywholesale_balance.php"

revert:
	sed -i${SED_PARAM} "s/${VERSION}/{{VERSION}}/g" "README.txt"
	sed -i${SED_PARAM} "s/${RELEASE_DATE}/{{RELEASE_DATE}}/g" "README.txt"
	sed -i${SED_PARAM} "s/${VERSION:v%=%}/{{VERSION}}/g" "modules/addons/synergywholesale_balance/synergywholesale_balance.php"
	sed -i${SED_PARAM} "s/${VERSION:v%=%}/{{VERSION}}g" "modules/widgets/synergywholesale_balance.php"
	sed -i${SED_PARAM} "s/${SW_API_HOSTNAME}/{{API}}/g" "modules/widgets/synergywholesale_balance.php"

package:
	make replace
	zip -r "synergy-wholesale-account-balance-$(VERSION).zip" . -x  \
	'.DS_Store' '**/.DS_Store' '*.cache' '.git*' '*.md' 'Makefile' 'package.json' 'package-lock.json' \
	'composer.json' 'composer.lock' '*.xml' \
	'vendor/*' 'node_modules/*' '.git/*' 'tests/*'
	make revert

build:
	make replace
	make package
	make revert