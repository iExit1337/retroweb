<?php

namespace App\Model\Error;

use System\App\Model\AbstractFactoryModel;

class ErrorFactory extends AbstractFactoryModel {

	/**
	 * @return string
	 */
	protected function getTable(): string {

		return "cms_errors";
	}

	/**
	 * @return bool
	 */
	protected function hasChildren(): bool {

		return true;
	}

	/**
	 * @return string
	 */
	protected function getChildrenInstance(): string {

		return Error::class;
	}
}