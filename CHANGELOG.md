*__note:__ the # at the end of lines are the pull request numbers on GitHub*

# Current

## Breaking Changes

* `KuzzleDataCollection` constructor signature has been changed from:  
`KuzzleDataCollection(kuzzle, index, collection)`  
 to:  
`KuzzleDataCollection(kuzzle, collection, index)`  
This has been done to make it on par with the `Kuzzle.dataCollectionFactory` method
