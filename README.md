# RestResources component

## Information

This component has for purpose to provide a simple REST API with no development.
 
## How to use
  - Your entity repository must implement our `ResourceRepositoryInterface`
  - Import our controller on your routing file 
  ``` 
    rest_resources:
       resource: '@RestResourcesBundle/Resources/config/routing.yaml'
  ```
  - Create your *.resource.yml `ex. users.resource.yml` file with the `restresources:file:create` command

## More

The API security is handled by the Symfony voters we use the following attributes : 
 - list
 - retrieve
 - create
 - update
 - delete
 - CREATE_{$childResource} __(this one is special see. Child resources)__
 ------
 ------
 - Each `GET` and `CGET` actions has a `_group` parameters which must match your serializer "Groups" annotation, the default used is `minimal`.
 - `EntityMetadataFilterTrait` allow you to filter on each entity fields of the resource
 
##Child resources

Sorry.
