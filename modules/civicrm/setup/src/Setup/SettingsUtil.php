<?php
namespace Civi\Setup;

/**
 * Helpers for working with "civicrm.settings.php.template" data.
 */
class SettingsUtil {

  public static function createParams(Model $m) {
    // Map from the logical $model to civicrm.settings.php variables.
    $params = array();
    $params['crmRoot'] = addslashes(rtrim($m->srcPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR);
    $params['templateCompileDir'] = addslashes($m->templateCompilePath);
    // ??why is frontEnd=0??
    $params['frontEnd'] = 0;
    $params['baseURL'] = addslashes(rtrim($m->cmsBaseUrl, '/'));
    $params['dbUser'] = addslashes(urlencode($m->db['username']));
    $params['dbPass'] = addslashes(urlencode($m->db['password'] ?? ''));
    $params['dbHost'] = addslashes(implode(':', array_map('urlencode', explode(':', $m->db['server']))));
    $params['dbName'] = addslashes(urlencode($m->db['database']));
    // The '&' prefix is awkward, but we don't know what's already in the file.
    // At the time of writing, it has ?new_link=true. If that is removed,
    // then need to update this.
    // The PHP_QUERY_RFC3986 is important because PEAR::DB will interpret plus
    // signs as a reference to its old DSN format and mangle the DSN, so we
    // need to use %20 for spaces.
    $params['dbSSL'] = empty($m->db['ssl_params']) ? '' : addslashes('&' . http_build_query($m->db['ssl_params'], '', '&', PHP_QUERY_RFC3986));
    $params['cms'] = addslashes($m->cms);
    $params['CMSdbUser'] = addslashes(urlencode($m->cmsDb['username']));
    $params['CMSdbPass'] = addslashes(urlencode($m->cmsDb['password']));
    $params['CMSdbHost'] = addslashes(implode(':', array_map('urlencode', explode(':', $m->cmsDb['server']))));
    $params['CMSdbName'] = addslashes(urlencode($m->cmsDb['database']));
    // The '&' prefix is awkward, but we don't know what's already in the file.
    // At the time of writing, it has ?new_link=true. If that is removed,
    // then need to update this.
    // The PHP_QUERY_RFC3986 is important because PEAR::DB will interpret plus
    // signs as a reference to its old DSN format and mangle the DSN, so we
    // need to use %20 for spaces.
    $params['CMSdbSSL'] = empty($m->cmsDb['ssl_params']) ? '' : addslashes('&' . http_build_query($m->cmsDb['ssl_params'], '', '&', PHP_QUERY_RFC3986));
    $params['siteKey'] = addslashes($m->siteKey);
    $params['credKeys'] = addslashes(implode(' ', $m->credKeys));
    $params['signKeys'] = addslashes(implode(' ', $m->signKeys));
    $params['deployID'] = addslashes($m->deployID);

    $extraSettings = [];

    foreach ($m->paths as $key => $aspects) {
      foreach ($aspects as $aspect => $value) {
        $extraSettings[] = sprintf('$civicrm_paths[%s][%s] = %s;', var_export($key, 1), var_export($aspect, 1), var_export($value, 1));
      }
    }

    foreach ($m->mandatorySettings as $key => $value) {
      $extraSettings[] = sprintf('$civicrm_setting[%s][%s] = %s;', '\'domain\'', var_export($key, 1), var_export($value, 1));
    }

    // FIXME $m->defaultSettings, $m->components, $m->extensions, $m->callbacks

    if ($extraSettings) {
      $params['extraSettings'] = "Additional settings generated by installer:\n" . implode("\n", $extraSettings);
    }
    else {
      $params['extraSettings'] = "";
    }

    return $params;
  }

  /**
   * Evaluate the settings template.
   *
   * @param string $tplPath
   *   Readable path of the 'civicrm.settings.php.template' file.
   * @param array $params
   *   List of values to pass into the template.
   * @return string
   *   Evaluated template.
   */
  public static function evaluate(string $tplPath, array $params): string {
    $template = file_get_contents($tplPath);
    $vars = [];
    foreach ($params as $key => $value) {
      $vars['%%' . $key . '%%'] = $value;
    }
    return trim(strtr($template, $vars)) . "\n";
  }

}