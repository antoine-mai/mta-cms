<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Extension\CoreExtension;
use Twig\Extension\SandboxExtension;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;
use Twig\TemplateWrapper;

/* admin/index.html.twig */
class __TwigTemplate_29e1b4c265325675b8bd45bd654d8a70 extends Template
{
    private Source $source;
    /**
     * @var array<string, Template>
     */
    private array $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
        ];
    }

    protected function doDisplay(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "admin/index.html.twig"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "admin/index.html.twig"));

        // line 1
        yield "<!DOCTYPE html>
<html>
<head>
    <meta charset=\"UTF-8\">
    <title>MTA-APP | Admin Dashboard</title>
    <style>
        body { font-family: sans-serif; background: #f4f4f9; color: #333; margin: 0; padding: 20px; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #4a90e2; }
        .stats { display: flex; gap: 20px; margin-top: 20px; }
        .card { flex: 1; padding: 20px; background: #eef2f7; border-radius: 6px; }
        .card h3 { margin-top: 0; font-size: 14px; text-transform: uppercase; color: #666; }
        .nav { margin-top: 30px; border-top: 1px solid #eee; padding-top: 20px; }
        .nav a { color: #4a90e2; text-decoration: none; margin-right: 15px; }
    </style>
</head>
<body>
    <div class=\"container\">
        <h1>Admin Dashboard</h1>
        <p>Welcome, <strong>";
        // line 20
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape((isset($context["admin_user"]) || array_key_exists("admin_user", $context) ? $context["admin_user"] : (function () { throw new RuntimeError('Variable "admin_user" does not exist.', 20, $this->source); })()), "html", null, true);
        yield "</strong>!</p>
        <p>This is a server-side rendered page (No React).</p>

        <div class=\"stats\">
            <div class=\"card\">
                <h3>Admin Path</h3>
                <p>";
        // line 26
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape((isset($context["admin_path"]) || array_key_exists("admin_path", $context) ? $context["admin_path"] : (function () { throw new RuntimeError('Variable "admin_path" does not exist.', 26, $this->source); })()), "html", null, true);
        yield "</p>
            </div>
            <div class=\"card\">
                <h3>PHP Version</h3>
                <p>";
        // line 30
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(Twig\Extension\CoreExtension::constant("PHP_VERSION"), "html", null, true);
        yield "</p>
            </div>
        </div>

        <div class=\"nav\">
            <a href=\"";
        // line 35
        yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("index");
        yield "\">Back to Site</a>
            <a href=\"";
        // line 36
        yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("admin_settings");
        yield "\">Settings</a>
            <a href=\"";
        // line 37
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape((isset($context["admin_path"]) || array_key_exists("admin_path", $context) ? $context["admin_path"] : (function () { throw new RuntimeError('Variable "admin_path" does not exist.', 37, $this->source); })()), "html", null, true);
        yield "/logout\">Logout</a>
        </div>
    </div>
</body>
</html>
";
        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "admin/index.html.twig";
    }

    /**
     * @codeCoverageIgnore
     */
    public function isTraitable(): bool
    {
        return false;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getDebugInfo(): array
    {
        return array (  101 => 37,  97 => 36,  93 => 35,  85 => 30,  78 => 26,  69 => 20,  48 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("<!DOCTYPE html>
<html>
<head>
    <meta charset=\"UTF-8\">
    <title>MTA-APP | Admin Dashboard</title>
    <style>
        body { font-family: sans-serif; background: #f4f4f9; color: #333; margin: 0; padding: 20px; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #4a90e2; }
        .stats { display: flex; gap: 20px; margin-top: 20px; }
        .card { flex: 1; padding: 20px; background: #eef2f7; border-radius: 6px; }
        .card h3 { margin-top: 0; font-size: 14px; text-transform: uppercase; color: #666; }
        .nav { margin-top: 30px; border-top: 1px solid #eee; padding-top: 20px; }
        .nav a { color: #4a90e2; text-decoration: none; margin-right: 15px; }
    </style>
</head>
<body>
    <div class=\"container\">
        <h1>Admin Dashboard</h1>
        <p>Welcome, <strong>{{ admin_user }}</strong>!</p>
        <p>This is a server-side rendered page (No React).</p>

        <div class=\"stats\">
            <div class=\"card\">
                <h3>Admin Path</h3>
                <p>{{ admin_path }}</p>
            </div>
            <div class=\"card\">
                <h3>PHP Version</h3>
                <p>{{ constant('PHP_VERSION') }}</p>
            </div>
        </div>

        <div class=\"nav\">
            <a href=\"{{ path('index') }}\">Back to Site</a>
            <a href=\"{{ path('admin_settings') }}\">Settings</a>
            <a href=\"{{ admin_path }}/logout\">Logout</a>
        </div>
    </div>
</body>
</html>
", "admin/index.html.twig", "/home/antoine/Workspace/htdocs/mta-cms/templates/admin/index.html.twig");
    }
}
