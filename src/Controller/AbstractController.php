<?php

namespace App\Controller;

use Twig\Environment;
use Twig\TwigFunction;
use Twig\Loader\FilesystemLoader;
use Twig\Extension\DebugExtension;

abstract class AbstractController
{
    /**
     * @var Environment
     */
    protected $twig;


    /**
     *  Initializes this class.
     */
    public function __construct()
    {
        $loader = new FilesystemLoader(APP_VIEW_PATH);
        if (APP_PROD) {
            $this->twig = new Environment($loader);
        } else {
            $this->twig = new Environment($loader, [
                "debug" => true,
            ]);
        }
        $this->twig->addExtension(new DebugExtension());
        if (isset($_SESSION["user"])) {
            $this->twig->addGlobal("appUser", $_SESSION["user"]);
        }
        $getFlash = new TwigFunction('getFlash', function () {
            $messages = [];
            if (isset($_SESSION['flash'])) {
                // clear messages after first read
                $messages = $_SESSION['flash'];
                unset($_SESSION['flash']);
            }
            return $messages;
        });
        $this->twig->addFunction($getFlash);
        $getToken = new TwigFunction('getToken', function () {
            if (isset($_SESSION['token'])) {
                $token = $_SESSION['token'];
            } else {
                $token = uniqid(rand(), true);
            }
            $_SESSION['token'] = $token;
            $hidden = '<input type="hidden" name="token" id="token" value="' .
                $token .
                '">';
            return $hidden;
        });
        $this->twig->addFunction($getToken);
        $domain = new TwigFunction('domain', function (string $value) {
            $host = $_SERVER['HTTP_HOST'];
            $http = FORCE_HTTPS ? 'https://' : 'http://';
            $url = $http . $host . $value;
            return $url;
        });
        $this->twig->addFunction($domain);
    }

    public function addFlash(string $color, string $message): void
    {
        $_SESSION['flash'] = $_SESSION['flash'] ?? [];
        array_push($_SESSION['flash'], [
            "color" => $color,
            "message" => $message,
        ]);
    }

    public function redirectTo(string $route): void
    {
        header("Location: $route");
        exit;
    }

    public function checkToken(string $token): bool
    {
        $validToken = false;
        $sessionToken = $_SESSION['token'] ?? "";
        if ($sessionToken && $token) {
            if ($_SESSION['token'] == $token) {
                $validToken = true;
            }
        }
        return $validToken;
    }

    public function stripData(array $data): array
    {
        function stripThisLevel($dataForThisLevel)
        {
            $strippedDataForThisLevel = [];
            if (gettype($dataForThisLevel) == 'string') {
                return strip_tags($dataForThisLevel);
            }
            if (gettype($dataForThisLevel) == 'integer') {
                return $dataForThisLevel;
            }
            if (gettype($dataForThisLevel) == 'array') {
                foreach ($dataForThisLevel as $keyNextLevel => $valueNextLevel) {
                    $strippedDataForThisLevel[$keyNextLevel] = stripThisLevel($valueNextLevel);
                }
            } else {
                return $dataForThisLevel;
            }
            return $strippedDataForThisLevel;
        }

        $strippedData = [];
        foreach ($data as $key => $value) {
            $strippedData[$key] = stripThisLevel($value);
        }
        return $strippedData;
    }
}
