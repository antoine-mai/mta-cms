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

/* admin/components/_header.html.twig */
class __TwigTemplate_ebc2ccfa3d8f5fb63ee705dcbee4e002 extends Template
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
            'breadcrumb' => [$this, 'block_breadcrumb'],
        ];
    }

    protected function doDisplay(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "admin/components/_header.html.twig"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "admin/components/_header.html.twig"));

        // line 1
        yield "<header class=\"h-14 flex items-center justify-between px-6 bg-white border-b border-border-light shrink-0 z-30 sticky top-0\">
\t<div class=\"flex items-center gap-2 text-[12px]\">
\t\t<a class=\"text-text-secondary hover:text-primary transition-colors font-normal\" href=\"";
        // line 3
        yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("index");
        yield "\">Site</a>
\t\t<span class=\"text-text-muted\">/</span>
\t\t<a class=\"text-text-secondary hover:text-primary transition-colors font-normal\" href=\"";
        // line 5
        yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("admin_dashboard");
        yield "\">Admin</a>
\t\t<span class=\"text-text-muted\">/</span>
\t\t<span class=\"text-text-main font-medium\">
\t\t\t";
        // line 8
        yield from $this->unwrap()->yieldBlock('breadcrumb', $context, $blocks);
        // line 10
        yield "\t\t</span>
\t</div>
\t<div class=\"flex items-center gap-5\">
\t\t<div class=\"hidden lg:flex items-center bg-gray-50 rounded-lg px-3 py-1.5 border border-border-light w-56 hover:border-gray-300 transition-colors\">
\t\t\t<span class=\"material-symbols-outlined text-text-secondary text-[18px]\">search</span>
\t\t\t<input class=\"bg-transparent border-none text-[12px] text-text-main focus:ring-0 w-full placeholder-text-secondary p-0 ml-2 font-normal\" placeholder=\"Search data...\" type=\"text\"/>
\t\t\t<span class=\"text-[9px] text-text-secondary border border-gray-200 px-1.5 py-0.5 rounded bg-white shadow-sm ml-2 font-bold whitespace-nowrap\">⌘ K</span>
\t\t</div>
\t\t<div class=\"h-5 w-px bg-border-light\"></div>
\t\t<div class=\"flex items-center gap-2\">
\t\t\t<button class=\"relative p-2 text-text-secondary hover:text-primary hover:bg-blue-50 rounded-lg transition-all flex items-center justify-center\">
\t\t\t\t<span class=\"material-symbols-outlined text-[20px]\">notifications</span>
\t\t\t\t<span class=\"absolute top-2 right-2 size-2 bg-red-500 rounded-full border-2 border-white\"></span>
\t\t\t</button>
\t\t\t<button class=\"p-2 text-text-secondary hover:text-primary hover:bg-blue-50 rounded-lg transition-all flex items-center justify-center\">
\t\t\t\t<span class=\"material-symbols-outlined text-[20px]\">help</span>
\t\t\t</button>
\t\t</div>
\t</div>
</header>
";
        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        yield from [];
    }

    // line 8
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_breadcrumb(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "breadcrumb"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "breadcrumb"));

        yield "Overview
\t\t\t";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "admin/components/_header.html.twig";
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
        return array (  97 => 8,  66 => 10,  64 => 8,  58 => 5,  53 => 3,  49 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("<header class=\"h-14 flex items-center justify-between px-6 bg-white border-b border-border-light shrink-0 z-30 sticky top-0\">
\t<div class=\"flex items-center gap-2 text-[12px]\">
\t\t<a class=\"text-text-secondary hover:text-primary transition-colors font-normal\" href=\"{{ path('index') }}\">Site</a>
\t\t<span class=\"text-text-muted\">/</span>
\t\t<a class=\"text-text-secondary hover:text-primary transition-colors font-normal\" href=\"{{ path('admin_dashboard') }}\">Admin</a>
\t\t<span class=\"text-text-muted\">/</span>
\t\t<span class=\"text-text-main font-medium\">
\t\t\t{% block breadcrumb %}Overview
\t\t\t{% endblock %}
\t\t</span>
\t</div>
\t<div class=\"flex items-center gap-5\">
\t\t<div class=\"hidden lg:flex items-center bg-gray-50 rounded-lg px-3 py-1.5 border border-border-light w-56 hover:border-gray-300 transition-colors\">
\t\t\t<span class=\"material-symbols-outlined text-text-secondary text-[18px]\">search</span>
\t\t\t<input class=\"bg-transparent border-none text-[12px] text-text-main focus:ring-0 w-full placeholder-text-secondary p-0 ml-2 font-normal\" placeholder=\"Search data...\" type=\"text\"/>
\t\t\t<span class=\"text-[9px] text-text-secondary border border-gray-200 px-1.5 py-0.5 rounded bg-white shadow-sm ml-2 font-bold whitespace-nowrap\">⌘ K</span>
\t\t</div>
\t\t<div class=\"h-5 w-px bg-border-light\"></div>
\t\t<div class=\"flex items-center gap-2\">
\t\t\t<button class=\"relative p-2 text-text-secondary hover:text-primary hover:bg-blue-50 rounded-lg transition-all flex items-center justify-center\">
\t\t\t\t<span class=\"material-symbols-outlined text-[20px]\">notifications</span>
\t\t\t\t<span class=\"absolute top-2 right-2 size-2 bg-red-500 rounded-full border-2 border-white\"></span>
\t\t\t</button>
\t\t\t<button class=\"p-2 text-text-secondary hover:text-primary hover:bg-blue-50 rounded-lg transition-all flex items-center justify-center\">
\t\t\t\t<span class=\"material-symbols-outlined text-[20px]\">help</span>
\t\t\t</button>
\t\t</div>
\t</div>
</header>
", "admin/components/_header.html.twig", "/home/antoine/Workspace/htdocs/mta-cms/templates/admin/components/_header.html.twig");
    }
}
