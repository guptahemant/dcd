<?php

namespace Drupal\config_filter\Config;

use Drupal\Core\Config\StorageInterface;

/**
 * Interface StorageFilterInterface.
 *
 * This interface defines a config storage filter as the FilteredStorage expects
 * to use when filtering the operations on the storage. The ConfigFilter plugin
 * interface extends this interface, together with plugin related interfaces.
 *
 * A well-behaved filter does not perform any write operation in a read method.
 *
 * @package Drupal\config_split\Config
 */
interface StorageFilterInterface {

  /**
   * Sets the source config storage on which the operation is performed.
   *
   * The storage is given to the filter when the storage wrapper is set up,
   * to avoid passing the storage to each of the filters so that they can read
   * from it before writing filtered config. The storage is read-only, use the
   * decorated storage to allow all filters to work for write operations.
   *
   * @param \Drupal\Core\Config\StorageInterface $storage
   *   The storage on which the operation is performed.
   */
  public function setSourceStorage(StorageInterface $storage);

  /**
   * Sets the wrapped config storage which is using the filter.
   *
   * This storage is available to the filter in order to inspect how the end
   * result looks like. This is useful for reading configuration from the
   * storage as drupal will. Beware of recursive calls to the filter.
   *
   * @param \Drupal\Core\Config\StorageInterface $storage
   *   The storage which has the filters applied.
   */
  public function setFilteredStorage(StorageInterface $storage);

  /**
   * Filters configuration data after it is read from the storage.
   *
   * @param string $name
   *   The name of a configuration object to load.
   * @param array|bool $data
   *   The configuration data to filter.
   *
   * @return array|bool
   *   The filtered data.
   */
  public function filterRead($name, $data);

  /**
   * Filter configuration data before it is written to the storage.
   *
   * @param string $name
   *   The name of a configuration object to save.
   * @param array $data
   *   The configuration data to filter.
   *
   * @return array|null
   *   The filtered data.
   */
  public function filterWrite($name, array $data);

  /**
   * Let the filter decide whether not-writing data should mean delete.
   *
   * Filters can return NULL for `filterWrite($name, $data)` which means to
   * not write the data to the source storage, but it can also mean deleting it.
   *
   * @param string $name
   *   The name of a configuration object to save.
   *
   * @return bool|null
   *   True to delete at the end of a filtered write action.
   */
  public function filterWriteEmptyIsDelete($name);

  /**
   * Filters whether a configuration object exists.
   *
   * @param string $name
   *   The name of a configuration object to test.
   * @param bool $exists
   *   The previous result to alter.
   *
   * @return bool
   *   TRUE if the configuration object exists, FALSE otherwise.
   */
  public function filterExists($name, $exists);

  /**
   * Deletes a configuration object from the storage.
   *
   * @param string $name
   *   The name of a configuration object to delete.
   * @param bool $delete
   *   Whether the previous filter allows to delete.
   *
   * @return bool
   *   TRUE to allow deletion, FALSE otherwise.
   */
  public function filterDelete($name, $delete);

  /**
   * Filters read configuration data from the storage.
   *
   * @param array $names
   *   List of names of the configuration objects to load.
   * @param array $data
   *   A list of the configuration data stored for the configuration object name
   *   that could be loaded for the passed list of names.
   *
   * @return array
   *   A list of the configuration data stored for the configuration object name
   *   that could be loaded for the passed list of names.
   */
  public function filterReadMultiple(array $names, array $data);

  /**
   * Filters renaming a configuration object in the storage.
   *
   * @param string $name
   *   The name of a configuration object to rename.
   * @param string $new_name
   *   The new name of a configuration object.
   * @param bool $rename
   *   Allowing renaming by previous filters.
   *
   * @return bool
   *   TRUE to allow renaming, FALSE otherwise.
   */
  public function filterRename($name, $new_name, $rename);

  /**
   * Filters what listAll should return.
   *
   * @param string $prefix
   *   The prefix to search for. If omitted, all configuration object
   *   names that exist are returned.
   * @param array $data
   *   The data returned by the storage.
   *
   * @return array
   *   The filtered configuration set.
   */
  public function filterListAll($prefix, array $data);

  /**
   * Deletes configuration objects whose names start with a given prefix.
   *
   * Given the following configuration object names:
   * - node.type.article
   * - node.type.page.
   *
   * Passing the prefix 'node.type.' will delete the above configuration
   * objects.
   *
   * @param string $prefix
   *   The prefix to search for. If omitted, all configuration
   *   objects that exist will be deleted.
   * @param bool $delete
   *   Whether to delete all or not.
   *
   * @return bool
   *   TRUE to allow deleting all, FALSE to trigger individual deletion.
   */
  public function filterDeleteAll($prefix, $delete);

  /**
   * Allows the filter to react on creating a collection on the storage.
   *
   * A configuration storage can contain multiple sets of configuration objects
   * in partitioned collections. The collection name identifies the current
   * collection used.
   *
   * @param string $collection
   *   The collection name. Valid collection names conform to the following
   *   regex [a-zA-Z_.]. A storage does not need to have a collection set.
   *   However, if a collection is set, then the storage should use it to store
   *   configuration in a way that allows retrieval of configuration for a
   *   particular collection.
   *
   * @return \Drupal\config_filter\Config\StorageFilterInterface|null
   *   Return a filter that should participate in the collection. This allows
   *   filters to act on different collections. Note that a new instance of the
   *   filter should be created rather than returning $this directly.
   */
  public function filterCreateCollection($collection);

  /**
   * Filter getting the existing collections.
   *
   * A configuration storage can contain multiple sets of configuration objects
   * in partitioned collections. The collection key name identifies the current
   * collection used.
   *
   * @param string[] $collections
   *   The array of existing collection names.
   *
   * @return array
   *   An array of existing collection names.
   */
  public function filterGetAllCollectionNames(array $collections);

  /**
   * Filter the name of the current collection the storage is using.
   *
   * @param string $collection
   *   The collection found by the storage.
   *
   * @return string
   *   The current collection name.
   */
  public function filterGetCollectionName($collection);

}
