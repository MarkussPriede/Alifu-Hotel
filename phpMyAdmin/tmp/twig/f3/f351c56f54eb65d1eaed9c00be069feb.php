<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Extension\SandboxExtension;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;

/* database/structure/tracking_icon.twig */
class __TwigTemplate_bc345a31354c3490ffa1528d3fa6bb0a extends Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
        ];
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 1
        echo "<a href=\"";
        echo PhpMyAdmin\Url::getFromRoute("/table/tracking", ["table" => ($context["table"] ?? null), "db" => ($context["db"] ?? null)]);
        echo "\">
    ";
        // line 2
        if (($context["is_tracked"] ?? null)) {
            // line 3
            echo PhpMyAdmin\Html\Generator::getImage("eye", _gettext("Tracking is active."));
        } else {
            // line 5
            echo PhpMyAdmin\Html\Generator::getImage("eye_grey", _gettext("Tracking is not active."));
        }
        // line 7
        echo "</a>
";
    }

    public function getTemplateName()
    {
        return "database/structure/tracking_icon.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  50 => 7,  47 => 5,  44 => 3,  42 => 2,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "database/structure/tracking_icon.twig", "C:\\xampp\\phpMyAdmin\\templates\\database\\structure\\tracking_icon.twig");
    }
}
