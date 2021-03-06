<?php
namespace FluidTYPO3\Development;

/*
 * This file is part of the FluidTYPO3/Development project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Package\FailsafePackageManager;
use TYPO3\CMS\Core\Package\Package;
use TYPO3\Flow\Core\Bootstrap as FlowBootstrap;
use TYPO3\Flow\Package\PackageInterface;

/**
 * Class NullPackageManager
 */
class NullPackageManager extends FailsafePackageManager {

	/**
	 * @var array
	 */
	protected $packageStatesConfiguration = array(
		'packages' => array()
	);

	/**
	 * @param FlowBootstrap $bootstrap
	 */
	public function initialize(FlowBootstrap $bootstrap) {
		$this->bootstrap = $bootstrap;
	}

	/**
	 * @param string $packageKey
	 * @return boolean
	 */
	public function isPackageActive($packageKey) {
		return in_array($packageKey, $this->getLoadedPackageKeys());
	}

	/**
	 * @param string $packageKey
	 * @return boolean
	 */
	public function isPackageAvailable($packageKey) {
		return in_array($packageKey, $this->getLoadedPackageKeys());
	}

	/**
	 * @param string $packageKey
	 * @return PackageInterface
	 */
	public function getPackage($packageKey) {
		$path = realpath(dirname(__FILE__) . '/../../' . $packageKey) . '/';
		if (FALSE === file_exists($path . 'ext_emconf.php')) {
			$path = realpath(dirname(__FILE__) . '/../../../../') . '/';
		}
		$package = new Package($this, $packageKey, $path, $path . 'Classes/');
		return $package;
	}

	/**
	 * @return array
	 */
	protected function getLoadedPackageKeys() {
		$root = realpath(__DIR__ . '/../../../../');
		$composerFile = $root . '/composer.json';
		$parsed = json_decode(file_get_contents($composerFile), JSON_OBJECT_AS_ARRAY);
		$key = substr($parsed['name'], strpos($parsed['name'], '/') + 1);
		$loaded = array($key);
		if (TRUE === isset($parsed['require-dev'])) {
			foreach (array_keys($parsed['require-dev']) as $packageName) {
				$loaded[] = substr($packageName, strpos($packageName, '/') + 1);
			}
		}
		if (TRUE === isset($parsed['require'])) {
			foreach (array_keys($parsed['require']) as $packageName) {
				$loaded[] = substr($packageName, strpos($packageName, '/') + 1);
			}
		}
		return $loaded;
	}

}
