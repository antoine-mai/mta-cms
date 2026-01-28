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

/* admin/login.html.twig */
class __TwigTemplate_16a7cae8fe33fd41d4c112cd2dc8ae32 extends Template
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
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "admin/login.html.twig"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "admin/login.html.twig"));

        // line 1
        yield "<!DOCTYPE html>
<html>
\t<head>
\t\t<meta charset=\"UTF-8\">
\t\t<title>MTA-APP | Admin Login</title>
\t\t<style>
\t\t\tbody {
\t\t\t\tfont-family: sans-serif;
\t\t\t\tbackground: #0a0a0c;
\t\t\t\tcolor: #white;
\t\t\t\tdisplay: flex;
\t\t\t\talign-items: center;
\t\t\t\tjustify-content: center;
\t\t\t\theight: 100vh;
\t\t\t\tmargin: 0;
\t\t\t}
\t\t\t.login-card {
\t\t\t\tbackground: #141416;
\t\t\t\tpadding: 40px;
\t\t\t\tborder-radius: 12px;
\t\t\t\tborder: 1px solid rgba(255, 255, 255, 0.1);
\t\t\t\twidth: 100%;
\t\t\t\tmax-width: 400px;
\t\t\t\tbox-shadow: 0 10px 25px rgba(0, 0, 0, 0.5);
\t\t\t}
\t\t\th1 {
\t\t\t\tmargin-top: 0;
\t\t\t\tcolor: #fff;
\t\t\t\ttext-align: center;
\t\t\t\tfont-size: 24px;
\t\t\t\tmargin-bottom: 24px;
\t\t\t}
\t\t\t.form-group {
\t\t\t\tmargin-bottom: 20px;
\t\t\t}
\t\t\tlabel {
\t\t\t\tdisplay: block;
\t\t\t\tmargin-bottom: 8px;
\t\t\t\tcolor: #94a3b8;
\t\t\t\tfont-size: 14px;
\t\t\t}
\t\t\tinput {
\t\t\t\twidth: 100%;
\t\t\t\tpadding: 12px;
\t\t\t\tborder-radius: 8px;
\t\t\t\tborder: 1px solid rgba(255, 255, 255, 0.1);
\t\t\t\tbackground: #0a0a0c;
\t\t\t\tcolor: white;
\t\t\t\tbox-sizing: border-box;
\t\t\t}
\t\t\tinput:focus {
\t\t\t\toutline: none;
\t\t\t\tborder-color: #6366f1;
\t\t\t}
\t\t\tbutton {
\t\t\t\twidth: 100%;
\t\t\t\tpadding: 12px;
\t\t\t\tborder-radius: 8px;
\t\t\t\tborder: none;
\t\t\t\tbackground: #6366f1;
\t\t\t\tcolor: white;
\t\t\t\tfont-weight: 600;
\t\t\t\tcursor: pointer;
\t\t\t\ttransition: background 0.2s;
\t\t\t\tmargin-top: 10px;
\t\t\t}
\t\t\tbutton:hover {
\t\t\t\tbackground: #4f46e5;
\t\t\t}
\t\t\t.error {
\t\t\t\tcolor: #ef4444;
\t\t\t\tbackground: rgba(239, 68, 68, 0.1);
\t\t\t\tpadding: 12px;
\t\t\t\tborder-radius: 8px;
\t\t\t\tmargin-bottom: 20px;
\t\t\t\tfont-size: 14px;
\t\t\t\ttext-align: center;
\t\t\t}
\t\t\t.back-link {
\t\t\t\tdisplay: block;
\t\t\t\ttext-align: center;
\t\t\t\tmargin-top: 20px;
\t\t\t\tcolor: #64748b;
\t\t\t\ttext-decoration: none;
\t\t\t\tfont-size: 14px;
\t\t\t}
\t\t\t.back-link:hover {
\t\t\t\tcolor: #94a3b8;
\t\t\t}
\t\t</style>
\t</head>
\t<body>
\t\t<div class=\"login-card\">
\t\t\t<h1>MTA-APP Admin</h1>

\t\t\t";
        // line 96
        $context['_parent'] = $context;
        $context['_seq'] = CoreExtension::ensureTraversable(CoreExtension::getAttribute($this->env, $this->source, (isset($context["app"]) || array_key_exists("app", $context) ? $context["app"] : (function () { throw new RuntimeError('Variable "app" does not exist.', 96, $this->source); })()), "flashes", ["error"], "method", false, false, false, 96));
        foreach ($context['_seq'] as $context["_key"] => $context["message"]) {
            // line 97
            yield "\t\t\t\t<div class=\"error\">";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($context["message"], "html", null, true);
            yield "</div>
\t\t\t";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_key'], $context['message'], $context['_parent']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 99
        yield "
\t\t\t";
        // line 100
        if ((($tmp = (isset($context["error"]) || array_key_exists("error", $context) ? $context["error"] : (function () { throw new RuntimeError('Variable "error" does not exist.', 100, $this->source); })())) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 101
            yield "\t\t\t\t<div class=\"error\">";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape((isset($context["error"]) || array_key_exists("error", $context) ? $context["error"] : (function () { throw new RuntimeError('Variable "error" does not exist.', 101, $this->source); })()), "html", null, true);
            yield "</div>
\t\t\t";
        }
        // line 103
        yield "
\t\t\t<form method=\"post\">
\t\t\t\t<div class=\"form-group\">
\t\t\t\t\t<label for=\"username\">Username</label>
\t\t\t\t\t<input type=\"text\" id=\"username\" name=\"username\" required autofocus>
\t\t\t\t</div>
\t\t\t\t<div class=\"form-group\">
\t\t\t\t\t<label for=\"password\">Password</label>
\t\t\t\t\t<input type=\"password\" id=\"password\" name=\"password\" required>
\t\t\t\t</div>
\t\t\t\t<button type=\"submit\">Login</button>
\t\t\t</form>

\t\t\t<a href=\"";
        // line 116
        yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("index");
        yield "\" class=\"back-link\">← Back to Site</a>
\t\t</div>
\t</body>
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
        return "admin/login.html.twig";
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
        return array (  184 => 116,  169 => 103,  163 => 101,  161 => 100,  158 => 99,  149 => 97,  145 => 96,  48 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("<!DOCTYPE html>
<html>
\t<head>
\t\t<meta charset=\"UTF-8\">
\t\t<title>MTA-APP | Admin Login</title>
\t\t<style>
\t\t\tbody {
\t\t\t\tfont-family: sans-serif;
\t\t\t\tbackground: #0a0a0c;
\t\t\t\tcolor: #white;
\t\t\t\tdisplay: flex;
\t\t\t\talign-items: center;
\t\t\t\tjustify-content: center;
\t\t\t\theight: 100vh;
\t\t\t\tmargin: 0;
\t\t\t}
\t\t\t.login-card {
\t\t\t\tbackground: #141416;
\t\t\t\tpadding: 40px;
\t\t\t\tborder-radius: 12px;
\t\t\t\tborder: 1px solid rgba(255, 255, 255, 0.1);
\t\t\t\twidth: 100%;
\t\t\t\tmax-width: 400px;
\t\t\t\tbox-shadow: 0 10px 25px rgba(0, 0, 0, 0.5);
\t\t\t}
\t\t\th1 {
\t\t\t\tmargin-top: 0;
\t\t\t\tcolor: #fff;
\t\t\t\ttext-align: center;
\t\t\t\tfont-size: 24px;
\t\t\t\tmargin-bottom: 24px;
\t\t\t}
\t\t\t.form-group {
\t\t\t\tmargin-bottom: 20px;
\t\t\t}
\t\t\tlabel {
\t\t\t\tdisplay: block;
\t\t\t\tmargin-bottom: 8px;
\t\t\t\tcolor: #94a3b8;
\t\t\t\tfont-size: 14px;
\t\t\t}
\t\t\tinput {
\t\t\t\twidth: 100%;
\t\t\t\tpadding: 12px;
\t\t\t\tborder-radius: 8px;
\t\t\t\tborder: 1px solid rgba(255, 255, 255, 0.1);
\t\t\t\tbackground: #0a0a0c;
\t\t\t\tcolor: white;
\t\t\t\tbox-sizing: border-box;
\t\t\t}
\t\t\tinput:focus {
\t\t\t\toutline: none;
\t\t\t\tborder-color: #6366f1;
\t\t\t}
\t\t\tbutton {
\t\t\t\twidth: 100%;
\t\t\t\tpadding: 12px;
\t\t\t\tborder-radius: 8px;
\t\t\t\tborder: none;
\t\t\t\tbackground: #6366f1;
\t\t\t\tcolor: white;
\t\t\t\tfont-weight: 600;
\t\t\t\tcursor: pointer;
\t\t\t\ttransition: background 0.2s;
\t\t\t\tmargin-top: 10px;
\t\t\t}
\t\t\tbutton:hover {
\t\t\t\tbackground: #4f46e5;
\t\t\t}
\t\t\t.error {
\t\t\t\tcolor: #ef4444;
\t\t\t\tbackground: rgba(239, 68, 68, 0.1);
\t\t\t\tpadding: 12px;
\t\t\t\tborder-radius: 8px;
\t\t\t\tmargin-bottom: 20px;
\t\t\t\tfont-size: 14px;
\t\t\t\ttext-align: center;
\t\t\t}
\t\t\t.back-link {
\t\t\t\tdisplay: block;
\t\t\t\ttext-align: center;
\t\t\t\tmargin-top: 20px;
\t\t\t\tcolor: #64748b;
\t\t\t\ttext-decoration: none;
\t\t\t\tfont-size: 14px;
\t\t\t}
\t\t\t.back-link:hover {
\t\t\t\tcolor: #94a3b8;
\t\t\t}
\t\t</style>
\t</head>
\t<body>
\t\t<div class=\"login-card\">
\t\t\t<h1>MTA-APP Admin</h1>

\t\t\t{% for message in app.flashes('error') %}
\t\t\t\t<div class=\"error\">{{ message }}</div>
\t\t\t{% endfor %}

\t\t\t{% if error %}
\t\t\t\t<div class=\"error\">{{ error }}</div>
\t\t\t{% endif %}

\t\t\t<form method=\"post\">
\t\t\t\t<div class=\"form-group\">
\t\t\t\t\t<label for=\"username\">Username</label>
\t\t\t\t\t<input type=\"text\" id=\"username\" name=\"username\" required autofocus>
\t\t\t\t</div>
\t\t\t\t<div class=\"form-group\">
\t\t\t\t\t<label for=\"password\">Password</label>
\t\t\t\t\t<input type=\"password\" id=\"password\" name=\"password\" required>
\t\t\t\t</div>
\t\t\t\t<button type=\"submit\">Login</button>
\t\t\t</form>

\t\t\t<a href=\"{{ path('index') }}\" class=\"back-link\">← Back to Site</a>
\t\t</div>
\t</body>
</html>
", "admin/login.html.twig", "/home/antoine/Workspace/htdocs/mta-cms/templates/admin/login.html.twig");
    }
}
