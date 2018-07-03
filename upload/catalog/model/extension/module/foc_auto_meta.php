<?php
class ModelExtensionModuleFocAutoMeta extends Model {

  const SETTINGS_GROUP = 'foc_auto_meta';
  const SETTINGS_GROUP_KEY = 'foc_auto_meta_data';

  private $__settings_loaded = false;
  private $__settings = null;

  public function getSettings () {
    $this->load->model('setting/setting');
    $language_id = $this->config->get('config_language_id');

    $settings = $this->model_setting_setting->getSetting(self::SETTINGS_GROUP);

    if (!is_null($settings)
        && isset($settings[self::SETTINGS_GROUP_KEY])
        && isset($settings[self::SETTINGS_GROUP_KEY][$language_id])
    ) {
      $this->__settings = $settings[self::SETTINGS_GROUP_KEY][$language_id];
      $this->__settings_loaded = true;
    }

    return $this->__settings;
  }

  public function getByKey ($key) {
    if (!$this->__settings_loaded) {
      $this->getSettings();
    }

    $key = self::SETTINGS_GROUP . '_' . $key;

    if (isset($this->__settings[$key])) {
      return $this->__settings[$key];
    }

    return null;
  }

  public function processTemplate ($key, $data) {
    $result = $this->getByKey($key);

    foreach ($data as $variable => $value) {
      $result = str_replace('{{ ' . $variable . ' }}', $value, $result);
    }

    return $result;
  }

  public function processProductMeta ($document, $product_info) {
    if (!$document->getTitle()
        || trim($document->getTitle()) == ''
        || $this->getByKey('force_replace_product_title')
    ) {
      $document->setTitle($this->processTemplate('product_title', $product_info));
    }

    if (!$document->getDescription()
        || trim($document->getDescription()) == ''
        || $this->getByKey('force_replace_product_description')
    ) {
      $document->setDescription($this->processTemplate('product_title', $product_info));
    }
  }

  public function processCategoryMeta ($document, $category_info) {
    if (!$document->getTitle()
        || trim($document->getTitle()) == ''
        || $this->getByKey('force_replace_category_title')
    ) {
      $document->setTitle($this->processTemplate('category_title', $category_info));
    }

    if (!$document->getDescription()
        || trim($document->getDescription()) == ''
        || $this->getByKey('force_replace_category_description')
    ) {
      $document->setDescription($this->processTemplate('category_title', $category_info));
    }
  }

}