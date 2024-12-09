<?php

namespace Tebex\Headless\Projects;

use InvalidArgumentException;
use Tebex\Headless;
use Tebex\Headless\BasketFacade;
use TebexHeadless\ApiException;
use TebexHeadless\Model\Category;
use TebexHeadless\Model\Package;

/**
 * Base class representing a Tebex store/project.
 * Each inheriting store type defines its required basket parameters and username parameters.
 */
abstract class TebexProject
{
    /**
     * Creates a basket for use on this project.
     *
     * @param string $completeUrl   The URL to send the user to after completion.
     * @param string $cancelUrl     The URL to send the user if they cancel the transaction.
     * @param string $userIdentifier        Optional username, email, etc. as required by the store type.
     * @param array $includedCreationData   Optional data to include with the basket creation request.
     * @return BasketFacade
     * @throws ApiException
     */
    public function createBasket(string $completeUrl, string $cancelUrl, string $userIdentifier="", array $includedCreationData=[]) : BasketFacade
    {
        $createBasketData = [
            "complete_url" => $completeUrl,
            "cancel_url" => $cancelUrl
        ];

        if (!empty($this->getUserIdentifierParameter())){
            if (empty($userIdentifier)) {
                throw new InvalidArgumentException("User identifier is required for this store type as: '" . $this->getUserIdentifierParameter() . "'");
            }

            // Set the user id to the one provided
            $createBasketData[$this->getUserIdentifierParameter()] = $userIdentifier;
        }

        // Confirm all required parameters for the store are present
        foreach ($this->getRequiredBasketParams() as $requiredParam){
            if (empty($requiredParam)){
                continue;
            }

            if (!isset($createBasketData[$requiredParam])){
                throw new InvalidArgumentException("Required parameter '" . $requiredParam . "' is missing from the basket creation data.");
            }
        }

        // Add any other data provided, such as custom
        foreach ($includedCreationData as $key => $value){
            if (!isset($createBasketData[$key])) {
                $createBasketData[$key] = $value;
            } else {
                throw new InvalidArgumentException("The key '" . $key . "' is already set in the basket creation data.");
            }
        }

        // Set redirect if not provided
        if (!isset($createBasketData["complete_auto_redirect"])) {
            $createBasketData["complete_auto_redirect"] = true;
        }

        // Create the remote basket and return it
        return new BasketFacade(Headless::createBasket($createBasketData), $this);
    }

    /**
     * Returns an array of parameters that are required in order to create the basket in addition to the default required parameters.
     * @return array|string[]
     */
    public function getRequiredBasketParams() : array {
        if (empty($this->getUserIdentifierParameter())) {
            return [];
        }

        return [$this->getUserIdentifierParameter()];
    }

    /**
     * Gets the name of the user identifier, such as username, username_id, etc. required by this store type.
     * @return string
     */
    public abstract function getUserIdentifierParameter() : string;

    /**
     * Gets the URL for a user to authorize their account. Baskets must be authorized before adding packages if required by the store.
     * After successfully authorizing, they will be directed to the provided $returnUrl.
     *
     * @param BasketFacade $wrapper Wrapped basket
     * @param string $returnUrl     The URL the user should return to after auth is completed.
     * @return string|null          The URL the user should authorize at. Direct the user to this URL.
     * @throws ApiException
     */
    public function getUserAuthUrl(BasketFacade $wrapper, string $returnUrl) : ?string
    {
        return Headless::getUserAuthUrl($wrapper->getBasket(), $returnUrl);
    }

    /**
     * Returns a list of all packages in the store.
     * @return array
     * @throws ApiException
     */
    public function listPackages() : array
    {
        return Headless::getAllPackages();
    }

    /**
     * Returns a list of all categories in the store as
     * @return array|Category
     * @throws ApiException
     */
    public function listCategories() : array
    {
        return Headless::getAllCategories();
    }

    /**
     * Gets the underlying name of the store's platform.
     * @return string
     */
    public function getPlatformName() : string
    {
        return Headless::getWebstore()->getPlatformType();
    }

    /**
     * @return bool True if the store requires the user to be authed before adding packages.
     */
    public function requiresUserAuth() : bool
    {
        return sizeof($this->getRequiredBasketParams()) > 0;
    }

    /**
     * Gets a category from the API by its ID.
     *
     * @param int $categoryId
     * @return Category
     * @throws ApiException
     */
    public function getCategory(int $categoryId): Category
    {
        return Headless::getCategory($categoryId);
    }

    /**
     * @throws ApiException
     */
    public function getPackage(int $packageId): Package
    {
        return Headless::getPackage($packageId);
    }
}