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

/* admin/components/_sidebar.html.twig */
class __TwigTemplate_59560af526bc695172238eb44b820b2d extends Template
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
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "admin/components/_sidebar.html.twig"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "admin/components/_sidebar.html.twig"));

        // line 1
        yield "<aside class=\"w-[80px] bg-sidebar-slim border-r border-border-light flex flex-col items-center py-6 gap-8 z-20 shrink-0\">
\t<div class=\"size-10 rounded-xl bg-gradient-to-br from-primary to-blue-600 flex items-center justify-center shadow-lg shadow-blue-500/20 overflow-hidden p-1\">
\t\t<img src=\"/mta.svg\" class=\"w-full h-full object-contain filter invert brightness-0\" alt=\"Logo\"/>
\t</div>
\t<nav class=\"flex flex-col gap-4 w-full px-2 items-center\">
\t\t<a href=\"";
        // line 6
        yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("admin_dashboard");
        yield "\" class=\"size-10 rounded-lg bg-primary flex items-center justify-center text-white shadow-lg shadow-blue-500/30 transition-all hover:scale-105 group relative\">
\t\t\t<span class=\"material-symbols-outlined filled\" style=\"font-variation-settings: 'FILL' 1;\">dashboard</span>
\t\t\t<span class=\"absolute left-14 bg-gray-900 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none whitespace-nowrap z-50 shadow-md font-medium\">Dashboard</span>
\t\t</a>
\t\t<button class=\"size-10 rounded-lg text-text-secondary hover:bg-white hover:text-primary hover:shadow-md flex items-center justify-center transition-all group relative\">
\t\t\t<span class=\"material-symbols-outlined\">folder_open</span>
\t\t\t<span class=\"absolute left-14 bg-gray-900 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none whitespace-nowrap z-50 shadow-md font-medium\">Projects</span>
\t\t</button>
\t\t<button class=\"size-10 rounded-lg text-text-secondary hover:bg-white hover:text-primary hover:shadow-md flex items-center justify-center transition-all group relative\">
\t\t\t<span class=\"material-symbols-outlined\">group</span>
\t\t\t<span class=\"absolute left-14 bg-gray-900 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none whitespace-nowrap z-50 shadow-md font-medium\">Team</span>
\t\t</button>
\t\t<button class=\"size-10 rounded-lg text-text-secondary hover:bg-white hover:text-primary hover:shadow-md flex items-center justify-center transition-all group relative\">
\t\t\t<span class=\"material-symbols-outlined\">calendar_month</span>
\t\t\t<span class=\"absolute left-14 bg-gray-900 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none whitespace-nowrap z-50 shadow-md font-medium\">Calendar</span>
\t\t</button>
\t\t<button class=\"size-10 rounded-lg text-text-secondary hover:bg-white hover:text-primary hover:shadow-md flex items-center justify-center transition-all group relative\">
\t\t\t<span class=\"material-symbols-outlined\">description</span>
\t\t\t<span class=\"absolute left-14 bg-gray-900 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none whitespace-nowrap z-50 shadow-md font-medium\">Documents</span>
\t\t</button>
\t</nav>
\t<div class=\"mt-auto flex flex-col gap-4\">
\t\t<a href=\"";
        // line 28
        yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("admin_settings");
        yield "\" class=\"size-10 rounded-lg text-text-secondary hover:bg-white hover:text-primary hover:shadow-md flex items-center justify-center transition-all\">
\t\t\t<span class=\"material-symbols-outlined\">settings</span>
\t\t</a>
\t\t<a href=\"";
        // line 31
        yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("admin_logout");
        yield "\" class=\"size-10 rounded-lg text-text-secondary hover:bg-red-50 hover:text-red-600 hover:shadow-md flex items-center justify-center transition-all group relative\" title=\"Logout\">
\t\t\t<span class=\"material-symbols-outlined\">logout</span>
\t\t\t<span class=\"absolute left-14 bg-gray-900 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none whitespace-nowrap z-50 shadow-md font-medium\">Logout</span>
\t\t</a>
\t</div>
</aside>

<aside class=\"w-[260px] bg-sidebar-wide border-r border-border-light flex flex-col py-6 px-4 gap-6 shrink-0 hidden md:flex\">
\t<div class=\"flex flex-col gap-1 px-2\">
\t\t<h2 class=\"text-text-main text-lg font-bold tracking-tight\">MTA CMS</h2>
\t\t<p class=\"text-text-secondary text-xs font-normal\">Administration Panel</p>
\t</div>
\t<div class=\"relative\">
\t\t<span class=\"material-symbols-outlined absolute left-3 top-2.5 text-text-secondary text-[18px]\">search</span>
\t\t<input class=\"w-full bg-white text-sm text-text-main placeholder-text-secondary rounded-lg pl-9 pr-4 py-2 border border-border-light focus:ring-1 focus:ring-primary focus:border-primary outline-none shadow-sm transition-shadow font-normal\" placeholder=\"Search module...\" type=\"text\"/>
\t</div>
\t<nav class=\"flex flex-col gap-1 flex-1 overflow-y-auto\">
\t\t<a class=\"flex items-center gap-3 px-3 py-2 rounded-lg bg-white text-primary border border-border-light shadow-sm font-medium\" href=\"";
        // line 48
        yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("admin_dashboard");
        yield "\">
\t\t\t<span class=\"material-symbols-outlined text-primary\" style=\"font-variation-settings: 'FILL' 1;\">pie_chart</span>
\t\t\t<span class=\"text-sm\">Overview</span>
\t\t</a>
\t\t<a class=\"flex items-center gap-3 px-3 py-2 rounded-lg text-text-secondary hover:bg-white hover:text-text-main hover:shadow-sm transition-all\" href=\"#\">
\t\t\t<span class=\"material-symbols-outlined\">ssid_chart</span>
\t\t\t<span class=\"text-sm font-normal\">Real-time View</span>
\t\t</a>
\t\t<div class=\"my-2 border-t border-gray-200\"></div>
\t\t<p class=\"px-3 text-[10px] font-bold text-text-muted uppercase tracking-widest mb-1\">System</p>
\t\t<a class=\"flex items-center gap-3 px-3 py-2 rounded-lg text-text-secondary hover:bg-white hover:text-text-main hover:shadow-sm transition-all\" href=\"";
        // line 58
        yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("admin_settings");
        yield "\">
\t\t\t<span class=\"material-symbols-outlined\">tune</span>
\t\t\t<span class=\"text-sm font-normal\">Configuration</span>
\t\t</a>
\t\t<a class=\"flex items-center gap-3 px-3 py-2 rounded-lg text-text-secondary hover:bg-white hover:text-text-main hover:shadow-sm transition-all\" href=\"";
        // line 62
        yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("admin_logout");
        yield "\">
\t\t\t<span class=\"material-symbols-outlined text-red-500\">logout</span>
\t\t\t<span class=\"text-sm font-normal\">Logout</span>
\t\t</a>
\t</nav>
</aside>
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
        return "admin/components/_sidebar.html.twig";
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
        return array (  126 => 62,  119 => 58,  106 => 48,  86 => 31,  80 => 28,  55 => 6,  48 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("<aside class=\"w-[80px] bg-sidebar-slim border-r border-border-light flex flex-col items-center py-6 gap-8 z-20 shrink-0\">
\t<div class=\"size-10 rounded-xl bg-gradient-to-br from-primary to-blue-600 flex items-center justify-center shadow-lg shadow-blue-500/20 overflow-hidden p-1\">
\t\t<img src=\"/mta.svg\" class=\"w-full h-full object-contain filter invert brightness-0\" alt=\"Logo\"/>
\t</div>
\t<nav class=\"flex flex-col gap-4 w-full px-2 items-center\">
\t\t<a href=\"{{ path('admin_dashboard') }}\" class=\"size-10 rounded-lg bg-primary flex items-center justify-center text-white shadow-lg shadow-blue-500/30 transition-all hover:scale-105 group relative\">
\t\t\t<span class=\"material-symbols-outlined filled\" style=\"font-variation-settings: 'FILL' 1;\">dashboard</span>
\t\t\t<span class=\"absolute left-14 bg-gray-900 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none whitespace-nowrap z-50 shadow-md font-medium\">Dashboard</span>
\t\t</a>
\t\t<button class=\"size-10 rounded-lg text-text-secondary hover:bg-white hover:text-primary hover:shadow-md flex items-center justify-center transition-all group relative\">
\t\t\t<span class=\"material-symbols-outlined\">folder_open</span>
\t\t\t<span class=\"absolute left-14 bg-gray-900 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none whitespace-nowrap z-50 shadow-md font-medium\">Projects</span>
\t\t</button>
\t\t<button class=\"size-10 rounded-lg text-text-secondary hover:bg-white hover:text-primary hover:shadow-md flex items-center justify-center transition-all group relative\">
\t\t\t<span class=\"material-symbols-outlined\">group</span>
\t\t\t<span class=\"absolute left-14 bg-gray-900 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none whitespace-nowrap z-50 shadow-md font-medium\">Team</span>
\t\t</button>
\t\t<button class=\"size-10 rounded-lg text-text-secondary hover:bg-white hover:text-primary hover:shadow-md flex items-center justify-center transition-all group relative\">
\t\t\t<span class=\"material-symbols-outlined\">calendar_month</span>
\t\t\t<span class=\"absolute left-14 bg-gray-900 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none whitespace-nowrap z-50 shadow-md font-medium\">Calendar</span>
\t\t</button>
\t\t<button class=\"size-10 rounded-lg text-text-secondary hover:bg-white hover:text-primary hover:shadow-md flex items-center justify-center transition-all group relative\">
\t\t\t<span class=\"material-symbols-outlined\">description</span>
\t\t\t<span class=\"absolute left-14 bg-gray-900 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none whitespace-nowrap z-50 shadow-md font-medium\">Documents</span>
\t\t</button>
\t</nav>
\t<div class=\"mt-auto flex flex-col gap-4\">
\t\t<a href=\"{{ path('admin_settings') }}\" class=\"size-10 rounded-lg text-text-secondary hover:bg-white hover:text-primary hover:shadow-md flex items-center justify-center transition-all\">
\t\t\t<span class=\"material-symbols-outlined\">settings</span>
\t\t</a>
\t\t<a href=\"{{ path('admin_logout') }}\" class=\"size-10 rounded-lg text-text-secondary hover:bg-red-50 hover:text-red-600 hover:shadow-md flex items-center justify-center transition-all group relative\" title=\"Logout\">
\t\t\t<span class=\"material-symbols-outlined\">logout</span>
\t\t\t<span class=\"absolute left-14 bg-gray-900 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none whitespace-nowrap z-50 shadow-md font-medium\">Logout</span>
\t\t</a>
\t</div>
</aside>

<aside class=\"w-[260px] bg-sidebar-wide border-r border-border-light flex flex-col py-6 px-4 gap-6 shrink-0 hidden md:flex\">
\t<div class=\"flex flex-col gap-1 px-2\">
\t\t<h2 class=\"text-text-main text-lg font-bold tracking-tight\">MTA CMS</h2>
\t\t<p class=\"text-text-secondary text-xs font-normal\">Administration Panel</p>
\t</div>
\t<div class=\"relative\">
\t\t<span class=\"material-symbols-outlined absolute left-3 top-2.5 text-text-secondary text-[18px]\">search</span>
\t\t<input class=\"w-full bg-white text-sm text-text-main placeholder-text-secondary rounded-lg pl-9 pr-4 py-2 border border-border-light focus:ring-1 focus:ring-primary focus:border-primary outline-none shadow-sm transition-shadow font-normal\" placeholder=\"Search module...\" type=\"text\"/>
\t</div>
\t<nav class=\"flex flex-col gap-1 flex-1 overflow-y-auto\">
\t\t<a class=\"flex items-center gap-3 px-3 py-2 rounded-lg bg-white text-primary border border-border-light shadow-sm font-medium\" href=\"{{ path('admin_dashboard') }}\">
\t\t\t<span class=\"material-symbols-outlined text-primary\" style=\"font-variation-settings: 'FILL' 1;\">pie_chart</span>
\t\t\t<span class=\"text-sm\">Overview</span>
\t\t</a>
\t\t<a class=\"flex items-center gap-3 px-3 py-2 rounded-lg text-text-secondary hover:bg-white hover:text-text-main hover:shadow-sm transition-all\" href=\"#\">
\t\t\t<span class=\"material-symbols-outlined\">ssid_chart</span>
\t\t\t<span class=\"text-sm font-normal\">Real-time View</span>
\t\t</a>
\t\t<div class=\"my-2 border-t border-gray-200\"></div>
\t\t<p class=\"px-3 text-[10px] font-bold text-text-muted uppercase tracking-widest mb-1\">System</p>
\t\t<a class=\"flex items-center gap-3 px-3 py-2 rounded-lg text-text-secondary hover:bg-white hover:text-text-main hover:shadow-sm transition-all\" href=\"{{ path('admin_settings') }}\">
\t\t\t<span class=\"material-symbols-outlined\">tune</span>
\t\t\t<span class=\"text-sm font-normal\">Configuration</span>
\t\t</a>
\t\t<a class=\"flex items-center gap-3 px-3 py-2 rounded-lg text-text-secondary hover:bg-white hover:text-text-main hover:shadow-sm transition-all\" href=\"{{ path('admin_logout') }}\">
\t\t\t<span class=\"material-symbols-outlined text-red-500\">logout</span>
\t\t\t<span class=\"text-sm font-normal\">Logout</span>
\t\t</a>
\t</nav>
</aside>
", "admin/components/_sidebar.html.twig", "/home/antoine/Workspace/htdocs/mta-cms/templates/admin/components/_sidebar.html.twig");
    }
}
