#!/bin/bash

set -eu

DOC_VERSION=3
DOC_PATH=/sdk/php/${DOC_VERSION}

# Used by vuepress
export DOC_DIR=$DOC_VERSION
export SITE_BASE=$DOC_PATH/
export CWD=`dirname "$0"`

# Used to specify --no-cache for example
ARGS=${2:-""}

case $1 in
  prepare)
    echo "Clone documentation framework"
    rm -rf $CWD/framework
    git clone --depth 10 --single-branch --branch master https://github.com/kuzzleio/documentation.git $CWD/framework/

    echo "Link local doc for dead links checking"
    rm $CWD/framework/src$DOC_PATH
    ln -s $CWD/../../../../$DOC_VERSION $CWD/framework/src$DOC_PATH

    echo "Install dependencies"
    npm --prefix $CWD/framework/ install
  ;;

  dev)
    $CWD/framework/node_modules/.bin/vuepress dev $CWD/$DOC_VERSION/ $ARGS
  ;;

  build)
    $CWD/framework/node_modules/.bin/vuepress build $CWD/$DOC_VERSION/ $ARGS
  ;;

  build-netlify)
    export SITE_BASE="/"
    $CWD/framework/node_modules/.bin/vuepress build $CWD/$DOC_VERSION/ $ARGS
  ;;

  upload)
    aws s3 sync $DOC_VERSION/.vuepress/dist s3://$S3_BUCKET$SITE_BASE --delete
  ;;

  cloudfront)
    aws cloudfront create-invalidation --distribution-id $CLOUDFRONT_DISTRIBUTION_ID --paths "$SITE_BASE*"
  ;;

  *)
    echo "Usage : $0 <prepare|dev|build|upload|cloudfront>"
    exit 1
  ;;
esac
