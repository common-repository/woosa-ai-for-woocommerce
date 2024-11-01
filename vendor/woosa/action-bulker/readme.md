## Introduction

This module gives the ability to extend easily the bulk action drop-down for any custom post types and comes with the following:

* It has the ability to initiate the module conditionally
* It performs the action either for each item individually or per the entire list of items
* It performs the action either via a scheduled action or instantly via a callback
* Gives the ability to run a custom validation either for each item individually or per the entire list of items before the action applies

## Optional

* Use module [Validation](https://gitlab.com/woosa/wp-plugin-modules/validation) for a built-in logic which already exdends this module and runs a validation per each item
* Use module [Action Scheduler](https://gitlab.com/woosa/wp-plugin-modules/action-scheduler) for a built-in logic which already exdends this module and performs scheduled actions

## Installation (via composer)

* **For plugins developed with our [boilerplate](https://gitlab.com/woosa/dev-tools/wp-plugin-starter):**

  * Either run `composer require woosa/action-bulker:version` or add `"woosa/action-bulker": "version"` in the `composer.json` file then run `npm start`

* **For plugins developed without our boilerplate:**

  * replace all occurences of `_wsa_namespace_` with your unique namespace
  * replace all occurences of `_wsa_text_domain_` with your translation text domain
  * the following constants should be defined in your plugin:
    * `define(__NAMESPACE__ . '\PREFIX', 'your_prefix_here');`

## Usage

How to initiate the module conditionally:

```php
add_filter(PREFIX . '\action_bulker\initiate', 'my_init_func');

function my_init_func($initiate, $post_type){

   $should_be_activated = false;

   //I want to disable the module if my condition is not met
   if( ! $should_be_activated ){
      $initiate = false;
   }

   return $initiate;
}
```

How to define and perform a bulk action using a callback:

```php
add_filter(PREFIX . '\action_bulker\actions', 'my_custom_actions');

function my_custom_actions($items){

   $items['my_action_id'] = [
      'label'         => __('Cool Action Label', 'my_text_domain'),
      'post_type'     => ['product'], //the post type where to add the action
      'callback'      => [__CLASS__, 'my_callback_function'],
      'bulk_perform'  => false, //whether or not to run the action for each item indivitually or per entire list of items
      'schedulable'   => false, //whether or not to be a scheduled action
      'validate_item' => false, //whether or not to run a validation per item
   ];

   return $items;
}

function my_callback_function($item_id){
   //do something with the $item_id
}
```

How to define and perform a bulk action using a callback with the `bulk_perform` enabled:

```php
add_filter(PREFIX . '\action_bulker\actions', 'my_custom_actions');

function my_custom_actions($items){

   $items['my_action_id'] = [
      'label'         => __('Cool Action Label', 'my_text_domain'),
      'post_type'     => ['product'], //the post type where to add the action
      'callback'      => [__CLASS__, 'my_callback_function'],
      'bulk_perform'  => true, //whether or not to run the action for each item indivitually or per entire list of items
      'schedulable'   => false, //whether or not to be a scheduled action
      'validate_item' => false, //whether or not to run a validation per item
   ];

   return $items;
}

function my_callback_function($items){

   foreach($items as $item_id){
      //do something with the $item_id
   }
}
```

How to define and perform a bulk action using the Task module:

```php
add_filter(PREFIX . '\action_bulker\actions', 'my_custom_actions');

function my_custom_actions($items){

   $items['my_action_id'] = [
      'label'     => __('Cool Action Label', 'my_text_domain'),
      'post_type' => ['product'], //the post type where to add the action
      'callback'  => [__CLASS__, 'my_callback_function'],
      'task'      => [ //whether the action to be processed via Task module
         'source' => 'my_source',
         'target' => 'my_target',
      ],
   ];

   return $items;
}

function my_callback_function($item_id, $bulk_action){

   $action = Util::unprefix($bulk_action['id']);
   $source = Util::array($bulk_action)->get('task/source');
   $target = Util::array($bulk_action)->get('task/target');

   Module_Task::update_entries([
      [
         'action'      => $action,
         'source'      => $source,
         'target'      => $target,
         'payload'     => get_my_payload($item_id),
         'resource_id' => $item_id,
      ]
   ]);
}
```

How to add an item validation:

```php
add_filter(PREFIX . '\action_bulker\validate_item', 'my_custom_validation');

function my_custom_validation($valid, $item_id){

   if(my_validation($item_id) === false){
      $valid = false;
   }

   return $valid;
}
```