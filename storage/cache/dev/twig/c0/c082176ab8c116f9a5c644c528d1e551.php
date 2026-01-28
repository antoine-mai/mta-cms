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

        $this->blocks = [
            'title' => [$this, 'block_title'],
            'breadcrumb' => [$this, 'block_breadcrumb'],
            'body' => [$this, 'block_body'],
        ];
    }

    protected function doGetParent(array $context): bool|string|Template|TemplateWrapper
    {
        // line 1
        return "admin.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "admin/index.html.twig"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "admin/index.html.twig"));

        $this->parent = $this->load("admin.html.twig", 1);
        yield from $this->parent->unwrap()->yield($context, array_merge($this->blocks, $blocks));
        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

    }

    // line 3
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_title(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "title"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "title"));

        yield "Admin Dashboard | MTA CMS
";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        yield from [];
    }

    // line 6
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
";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        yield from [];
    }

    // line 9
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_body(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "body"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "body"));

        // line 10
        yield "\t<div class=\"flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-2\">
\t\t<div>
\t\t\t<h1 class=\"text-2xl font-black text-text-main mb-1 tracking-tight\">Dashboard Overview</h1>
\t\t\t<p class=\"text-text-secondary text-sm font-normal\">Welcome back,
\t\t\t\t<strong>";
        // line 14
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape((isset($context["admin_user"]) || array_key_exists("admin_user", $context) ? $context["admin_user"] : (function () { throw new RuntimeError('Variable "admin_user" does not exist.', 14, $this->source); })()), "html", null, true);
        yield "</strong>! Here's what's happening today.</p>
\t\t</div>
\t\t<div class=\"flex items-center gap-2 bg-white border border-border-light rounded-lg p-1 shadow-sm\">
\t\t\t<button class=\"px-3 py-1 bg-gray-100 text-text-main text-[11px] font-bold rounded shadow-sm border border-gray-200\">Last 30 Days</button>
\t\t\t<button class=\"px-3 py-1 text-text-secondary hover:bg-gray-50 hover:text-text-main text-[11px] font-medium rounded transition-colors\">Q3 2023</button>
\t\t\t<button class=\"px-3 py-1 text-text-secondary hover:bg-gray-50 hover:text-text-main text-[11px] font-medium rounded transition-colors\">Year</button>
\t\t</div>
\t</div>

\t<div class=\"grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4\">
\t\t<div class=\"bg-card-bg p-5 rounded-xl border border-border-light hover:border-primary/50 transition-colors shadow-sm hover:shadow-md\">
\t\t\t<div class=\"flex justify-between items-start mb-4\">
\t\t\t\t<div class=\"p-2 rounded-lg bg-blue-50 text-primary\">
\t\t\t\t\t<span class=\"material-symbols-outlined\">payments</span>
\t\t\t\t</div>
\t\t\t\t<span class=\"flex items-center text-emerald-600 text-xs font-bold bg-emerald-50 px-2 py-1 rounded-full border border-emerald-100\">
\t\t\t\t\t<span class=\"material-symbols-outlined text-[14px] mr-1\">trending_up</span>+12%
\t\t\t\t</span>
\t\t\t</div>
\t\t\t<p class=\"text-text-secondary text-xs font-bold uppercase tracking-wider mb-1\">Total Revenue</p>
\t\t\t<h3 class=\"text-2xl font-black text-text-main tabular-nums\">\$45,231.89</h3>
\t\t</div>
\t\t<div class=\"bg-card-bg p-5 rounded-xl border border-border-light hover:border-primary/50 transition-colors shadow-sm hover:shadow-md\">
\t\t\t<div class=\"flex justify-between items-start mb-4\">
\t\t\t\t<div class=\"p-2 rounded-lg bg-purple-50 text-purple-600\">
\t\t\t\t\t<span class=\"material-symbols-outlined\">groups</span>
\t\t\t\t</div>
\t\t\t\t<span class=\"flex items-center text-emerald-600 text-xs font-bold bg-emerald-50 px-2 py-1 rounded-full border border-emerald-100\">
\t\t\t\t\t<span class=\"material-symbols-outlined text-[14px] mr-1\">trending_up</span>+5.2%
\t\t\t\t</span>
\t\t\t</div>
\t\t\t<p class=\"text-text-secondary text-xs font-bold uppercase tracking-wider mb-1\">Active Sessions</p>
\t\t\t<h3 class=\"text-2xl font-black text-text-main tabular-nums\">1,204</h3>
\t\t</div>
\t\t<div class=\"bg-card-bg p-5 rounded-xl border border-border-light hover:border-primary/50 transition-colors shadow-sm hover:shadow-md\">
\t\t\t<div class=\"flex justify-between items-start mb-4\">
\t\t\t\t<div class=\"p-2 rounded-lg bg-orange-50 text-orange-600\">
\t\t\t\t\t<span class=\"material-symbols-outlined\">output</span>
\t\t\t\t</div>
\t\t\t\t<span class=\"flex items-center text-red-600 text-xs font-bold bg-red-50 px-2 py-1 rounded-full border border-red-100\">
\t\t\t\t\t<span class=\"material-symbols-outlined text-[14px] mr-1\">trending_down</span>-2.1%
\t\t\t\t</span>
\t\t\t</div>
\t\t\t<p class=\"text-text-secondary text-xs font-bold uppercase tracking-wider mb-1\">PHP Version</p>
\t\t\t<h3 class=\"text-2xl font-black text-text-main tabular-nums\">";
        // line 58
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(Twig\Extension\CoreExtension::constant("PHP_VERSION"), "html", null, true);
        yield "</h3>
\t\t</div>
\t\t<div class=\"bg-card-bg p-5 rounded-xl border border-border-light hover:border-primary/50 transition-colors shadow-sm hover:shadow-md\">
\t\t\t<div class=\"flex justify-between items-start mb-4\">
\t\t\t\t<div class=\"p-2 rounded-lg bg-pink-50 text-pink-600\">
\t\t\t\t\t<span class=\"material-symbols-outlined\">schedule</span>
\t\t\t\t</div>
\t\t\t\t<span class=\"flex items-center text-emerald-600 text-xs font-bold bg-emerald-50 px-2 py-1 rounded-full border border-emerald-100\">
\t\t\t\t\t<span class=\"material-symbols-outlined text-[14px] mr-1\">trending_up</span>+10s
\t\t\t\t</span>
\t\t\t</div>
\t\t\t<p class=\"text-text-secondary text-xs font-bold uppercase tracking-wider mb-1\">Admin Path</p>
\t\t\t<h3 class=\"text-lg font-black text-text-main\">";
        // line 70
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape((isset($context["admin_path"]) || array_key_exists("admin_path", $context) ? $context["admin_path"] : (function () { throw new RuntimeError('Variable "admin_path" does not exist.', 70, $this->source); })()), "html", null, true);
        yield "</h3>
\t\t</div>
\t</div>

\t<div class=\"grid grid-cols-1 lg:grid-cols-3 gap-6\">
\t\t<div class=\"lg:col-span-2 bg-card-bg rounded-xl border border-border-light p-6 shadow-sm\">
\t\t\t<div class=\"flex items-center justify-between mb-6\">
\t\t\t\t<div>
\t\t\t\t\t<h3 class=\"text-text-main font-bold text-lg\">Traffic Overview</h3>
\t\t\t\t\t<p class=\"text-text-secondary text-sm font-normal\">Comparison with previous period</p>
\t\t\t\t</div>
\t\t\t\t<button class=\"text-primary hover:text-primary-hover text-xs font-bold flex items-center gap-1 transition-colors uppercase tracking-wider\">
\t\t\t\t\tDownload Report
\t\t\t\t\t<span class=\"material-symbols-outlined text-[16px]\">download</span>
\t\t\t\t</button>
\t\t\t</div>
\t\t\t<div class=\"relative h-[280px] w-full mt-4\">
\t\t\t\t<div class=\"absolute inset-0 flex flex-col justify-between text-text-secondary/60 text-[10px] font-bold\">
\t\t\t\t\t<div class=\"w-full border-b border-dashed border-gray-100 pb-1\">50K</div>
\t\t\t\t\t<div class=\"w-full border-b border-dashed border-gray-100 pb-1\">40K</div>
\t\t\t\t\t<div class=\"w-full border-b border-dashed border-gray-100 pb-1\">30K</div>
\t\t\t\t\t<div class=\"w-full border-b border-dashed border-gray-100 pb-1\">20K</div>
\t\t\t\t\t<div class=\"w-full border-b border-dashed border-gray-100 pb-1\">10K</div>
\t\t\t\t\t<div class=\"w-full border-b border-dashed border-gray-100 pb-1\">0</div>
\t\t\t\t</div>
\t\t\t\t<div class=\"absolute inset-0 pl-8 pb-6 flex items-end justify-between pt-4\">
\t\t\t\t\t<div class=\"absolute bottom-6 left-8 right-0 top-4 overflow-hidden\">
\t\t\t\t\t\t<svg class=\"w-full h-full\" preserveaspectratio=\"none\" viewbox=\"0 0 100 50\">
\t\t\t\t\t\t\t<path d=\"M0,45 C10,40 20,42 30,35 C40,28 50,32 60,25 C70,18 80,22 90,15 L100,10\" fill=\"none\" stroke=\"#cbd5e1\" stroke-dasharray=\"2,1\" stroke-width=\"0.5\"></path>
\t\t\t\t\t\t\t<defs>
\t\t\t\t\t\t\t\t<linearGradient id=\"gradientBlue\" x1=\"0\" x2=\"0\" y1=\"0\" y2=\"1\">
\t\t\t\t\t\t\t\t\t<stop offset=\"0%\" stop-color=\"#135bec\" stop-opacity=\"0.2\"></stop>
\t\t\t\t\t\t\t\t\t<stop offset=\"100%\" stop-color=\"#135bec\" stop-opacity=\"0\"></stop>
\t\t\t\t\t\t\t\t</linearGradient>
\t\t\t\t\t\t\t</defs>
\t\t\t\t\t\t\t<path d=\"M0,35 C10,38 20,30 30,25 C40,20 50,22 60,15 C70,8 80,12 90,5 L100,2 V50 H0 Z\" fill=\"url(#gradientBlue)\"></path>
\t\t\t\t\t\t\t<path d=\"M0,35 C10,38 20,30 30,25 C40,20 50,22 60,15 C70,8 80,12 90,5 L100,2\" fill=\"none\" stroke=\"#135bec\" stroke-width=\"0.8\"></path>
\t\t\t\t\t\t\t<circle cx=\"60\" cy=\"15\" fill=\"#ffffff\" r=\"1.5\" stroke=\"#135bec\" stroke-width=\"1\"></circle>
\t\t\t\t\t\t</svg>
\t\t\t\t\t\t<div class=\"absolute top-[20%] left-[58%] bg-white text-text-main text-[10px] px-2 py-1 rounded shadow-lg border border-border-light whitespace-nowrap z-10 font-bold\">
\t\t\t\t\t\t\t<div>2,450 Visits</div>
\t\t\t\t\t\t\t<div class=\"text-text-secondary font-medium\">Nov 14</div>
\t\t\t\t\t\t</div>
\t\t\t\t\t</div>
\t\t\t\t</div>
\t\t\t</div>
\t\t\t<div class=\"flex justify-between pl-8 text-[10px] text-text-secondary font-bold mt-2\">
\t\t\t\t<span>NOV 01</span>
\t\t\t\t<span>NOV 05</span>
\t\t\t\t<span>NOV 10</span>
\t\t\t\t<span>NOV 15</span>
\t\t\t\t<span>NOV 20</span>
\t\t\t\t<span>NOV 25</span>
\t\t\t\t<span>NOV 30</span>
\t\t\t</div>
\t\t</div>
\t\t<div class=\"lg:col-span-1 flex flex-col gap-6\">
\t\t\t<div class=\"bg-card-bg rounded-xl border border-border-light p-6 flex flex-col gap-4 shadow-sm flex-1\">
\t\t\t\t<h3 class=\"text-text-main font-bold text-lg\">Monthly Target</h3>
\t\t\t\t<div class=\"relative size-36 mx-auto my-2\">
\t\t\t\t\t<svg class=\"size-full -rotate-90\" viewbox=\"0 0 36 36\">
\t\t\t\t\t\t<path class=\"text-gray-100\" d=\"M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"3\"></path>
\t\t\t\t\t\t<path class=\"text-primary\" d=\"M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831\" fill=\"none\" stroke=\"currentColor\" stroke-dasharray=\"75, 100\" stroke-linecap=\"round\" stroke-width=\"3\"></path>
\t\t\t\t\t</svg>
\t\t\t\t\t<div class=\"absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 text-center\">
\t\t\t\t\t\t<span class=\"text-2xl font-black text-text-main tabular-nums\">75%</span>
\t\t\t\t\t\t<span class=\"block text-[9px] text-text-secondary font-black tracking-widest uppercase\">Achieved</span>
\t\t\t\t\t</div>
\t\t\t\t</div>
\t\t\t\t<div class=\"mt-auto space-y-3\">
\t\t\t\t\t<div class=\"flex justify-between text-xs\">
\t\t\t\t\t\t<span class=\"text-text-secondary font-medium\">Monthly Target</span>
\t\t\t\t\t\t<span class=\"text-text-main font-bold tabular-nums\">\$60,000.00</span>
\t\t\t\t\t</div>
\t\t\t\t\t<div class=\"flex justify-between text-xs\">
\t\t\t\t\t\t<span class=\"text-text-secondary font-medium\">Current Revenue</span>
\t\t\t\t\t\t<span class=\"text-text-main font-bold tabular-nums\">\$45,231.89</span>
\t\t\t\t\t</div>
\t\t\t\t</div>
\t\t\t</div>
\t\t</div>
\t</div>

\t<div class=\"bg-card-bg rounded-xl border border-border-light overflow-hidden shadow-sm mb-10\">
\t\t<div class=\"px-6 py-4 border-b border-border-light flex items-center justify-between\">
\t\t\t<h3 class=\"text-text-main font-bold text-lg\">Recent Transactions</h3>
\t\t\t<div class=\"flex gap-2\">
\t\t\t\t<button class=\"flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-border-light bg-white text-xs font-bold text-text-secondary hover:text-primary hover:border-primary transition-colors shadow-sm\">
\t\t\t\t\t<span class=\"material-symbols-outlined text-[16px]\">filter_list</span>
\t\t\t\t\tFILTER
\t\t\t\t</button>
\t\t\t\t<button class=\"flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-border-light bg-white text-xs font-bold text-text-secondary hover:text-primary hover:border-primary transition-colors shadow-sm\">
\t\t\t\t\t<span class=\"material-symbols-outlined text-[16px]\">download</span>
\t\t\t\t\tEXPORT
\t\t\t\t</button>
\t\t\t</div>
\t\t</div>
\t\t<div class=\"overflow-x-auto\">
\t\t\t<table class=\"w-full text-left text-sm border-collapse\">
\t\t\t\t<thead class=\"bg-gray-50 text-text-secondary border-b border-border-light\">
\t\t\t\t\t<tr>
\t\t\t\t\t\t<th class=\"px-6 py-3 font-bold text-xs uppercase tracking-wider\">Transaction ID</th>
\t\t\t\t\t\t<th class=\"px-6 py-3 font-bold text-xs uppercase tracking-wider\">User Account</th>
\t\t\t\t\t\t<th class=\"px-6 py-3 font-bold text-xs uppercase tracking-wider\">Amount</th>
\t\t\t\t\t\t<th class=\"px-6 py-3 font-bold text-xs uppercase tracking-wider\">Status</th>
\t\t\t\t\t\t<th class=\"px-6 py-3 font-bold text-xs uppercase tracking-wider\">Processed Date</th>
\t\t\t\t\t\t<th class=\"px-6 py-3 font-bold text-xs uppercase tracking-wider text-right\">Action</th>
\t\t\t\t\t</tr>
\t\t\t\t</thead>
\t\t\t\t<tbody class=\"divide-y divide-border-light text-text-main\">
\t\t\t\t\t<tr class=\"hover:bg-gray-50 transition-colors group\">
\t\t\t\t\t\t<td class=\"px-6 py-4 font-mono text-xs text-text-secondary font-medium\">#TRX-9821</td>
\t\t\t\t\t\t<td class=\"px-6 py-4\">
\t\t\t\t\t\t\t<div class=\"flex items-center gap-3\">
\t\t\t\t\t\t\t\t<div class=\"size-8 rounded-full bg-cover bg-center shadow-sm border border-gray-100\" style='background-image: url(\"https://lh3.googleusercontent.com/aida-public/AB6AXuCV6iIzeMGh2UPiS-yaqEdLXTHsdEoO-Wh9Z7S1TOh22HAbeOT2v5cLH9Fcp_UvrqFfd8j7pYJMVtB2IlWWlOe3oncBvR7kjjYYNFRLbcKktNjSrl6Tf9QmSeS2wZHoNHQ04LL9XnTbVu5a9YNmTtlHpWCjA8ZHBVBU8yzHvMj8l_PwIcpGUsHyTUZ3AMt47vgrClmutyptzyJ14wUWXggUjbcv4NWRmETrEUMcbdlZFs7_ty8VbI22bKd74ft3sjOTVgky_fzpKUqv\");'></div>
\t\t\t\t\t\t\t\t<div>
\t\t\t\t\t\t\t\t\t<p class=\"font-bold text-text-main text-sm\">Courtney Henry</p>
\t\t\t\t\t\t\t\t\t<p class=\"text-[10px] text-text-secondary font-medium uppercase tracking-tight\">Pro Member</p>
\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t</td>
\t\t\t\t\t\t<td class=\"px-6 py-4 font-bold text-text-main tabular-nums\">\$1,290.00</td>
\t\t\t\t\t\t<td class=\"px-6 py-4\">
\t\t\t\t\t\t\t<span class=\"inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-emerald-50 text-emerald-600 border border-emerald-100 tracking-wide\">
\t\t\t\t\t\t\t\t<span class=\"size-1 rounded-full bg-emerald-500\"></span>
\t\t\t\t\t\t\t\tPaid
\t\t\t\t\t\t\t</span>
\t\t\t\t\t\t</td>
\t\t\t\t\t\t<td class=\"px-6 py-4 text-text-secondary text-xs font-medium\">Nov 24, 2023</td>
\t\t\t\t\t\t<td class=\"px-6 py-4 text-right\">
\t\t\t\t\t\t\t<button class=\"text-text-secondary hover:text-primary p-1 rounded hover:bg-white transition-colors\">
\t\t\t\t\t\t\t\t<span class=\"material-symbols-outlined text-[18px]\">more_horiz</span>
\t\t\t\t\t\t\t</button>
\t\t\t\t\t\t</td>
\t\t\t\t\t</tr>
\t\t\t\t\t<tr class=\"hover:bg-gray-50 transition-colors group\">
\t\t\t\t\t\t<td class=\"px-6 py-4 font-mono text-xs text-text-secondary font-medium\">#TRX-9822</td>
\t\t\t\t\t\t<td class=\"px-6 py-4\">
\t\t\t\t\t\t\t<div class=\"flex items-center gap-3\">
\t\t\t\t\t\t\t\t<div class=\"size-8 rounded-full bg-cover bg-center shadow-sm border border-gray-100\" style='background-image: url(\"https://lh3.googleusercontent.com/aida-public/AB6AXuBFPjZVYEkCshBh_jI1qVqxhvugHlOWOOM9INapjSoaUEa7RnuIt5hkyjNkrsI55KvVMWRqUBtL1MFVBxMeK2Z7myyFansC58C0FI7QI3ODLJT9tHrSNLvgCD26I_Dm2DZkCuoSpPwU6sIplwYqcOwHsfh4d4vsmDMS7jfEfvMPfC-0ptXEofwhC_pHam_sVp-kXNMECtKMlnFWzapqVU0OAtZ4qaOnCAoyebrPh7Wt2hnt0SWz0RpI8dq8b35D3eZr2OBFtNp_OZc_\");'></div>
\t\t\t\t\t\t\t\t<div>
\t\t\t\t\t\t\t\t\t<p class=\"font-bold text-text-main text-sm\">Darrell Steward</p>
\t\t\t\t\t\t\t\t\t<p class=\"text-[10px] text-text-secondary font-medium uppercase tracking-tight\">Free Member</p>
\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t</td>
\t\t\t\t\t\t<td class=\"px-6 py-4 font-bold text-text-main tabular-nums\">\$59.00</td>
\t\t\t\t\t\t<td class=\"px-6 py-4\">
\t\t\t\t\t\t\t<span class=\"inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-yellow-50 text-yellow-600 border border-yellow-100 tracking-wide\">
\t\t\t\t\t\t\t\t<span class=\"size-1 rounded-full bg-yellow-500\"></span>
\t\t\t\t\t\t\t\tPending
\t\t\t\t\t\t\t</span>
\t\t\t\t\t\t</td>
\t\t\t\t\t\t<td class=\"px-6 py-4 text-text-secondary text-xs font-medium\">Nov 24, 2023</td>
\t\t\t\t\t\t<td class=\"px-6 py-4 text-right\">
\t\t\t\t\t\t\t<button class=\"text-text-secondary hover:text-primary p-1 rounded hover:bg-white transition-colors\">
\t\t\t\t\t\t\t\t<span class=\"material-symbols-outlined text-[18px]\">more_horiz</span>
\t\t\t\t\t\t\t</button>
\t\t\t\t\t\t</td>
\t\t\t\t\t</tr>
\t\t\t\t</tbody>
\t\t\t</table>
\t\t</div>
\t\t<div class=\"px-6 py-3 border-t border-border-light flex items-center justify-between text-[11px] text-text-secondary bg-gray-50/50\">
\t\t\t<span class=\"font-medium\">SHOWING
\t\t\t\t<span class=\"text-text-main font-bold\">2</span>
\t\t\t\tOF
\t\t\t\t<span class=\"text-text-main font-bold\">128</span>
\t\t\t\tTRANSACTIONS</span>
\t\t\t<div class=\"flex gap-2\">
\t\t\t\t<button class=\"px-3 py-1 rounded border border-border-light hover:bg-white hover:text-primary transition-colors bg-white shadow-sm font-bold uppercase tracking-tight\">Previous</button>
\t\t\t\t<button class=\"px-3 py-1 rounded border border-border-light hover:bg-white hover:text-primary transition-colors bg-white shadow-sm font-bold uppercase tracking-tight\">Next</button>
\t\t\t</div>
\t\t</div>
\t</div>
";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

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
        return array (  194 => 70,  179 => 58,  132 => 14,  126 => 10,  113 => 9,  89 => 6,  65 => 3,  42 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("{% extends 'admin.html.twig' %}

{% block title %}Admin Dashboard | MTA CMS
{% endblock %}

{% block breadcrumb %}Overview
{% endblock %}

{% block body %}
\t<div class=\"flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-2\">
\t\t<div>
\t\t\t<h1 class=\"text-2xl font-black text-text-main mb-1 tracking-tight\">Dashboard Overview</h1>
\t\t\t<p class=\"text-text-secondary text-sm font-normal\">Welcome back,
\t\t\t\t<strong>{{ admin_user }}</strong>! Here's what's happening today.</p>
\t\t</div>
\t\t<div class=\"flex items-center gap-2 bg-white border border-border-light rounded-lg p-1 shadow-sm\">
\t\t\t<button class=\"px-3 py-1 bg-gray-100 text-text-main text-[11px] font-bold rounded shadow-sm border border-gray-200\">Last 30 Days</button>
\t\t\t<button class=\"px-3 py-1 text-text-secondary hover:bg-gray-50 hover:text-text-main text-[11px] font-medium rounded transition-colors\">Q3 2023</button>
\t\t\t<button class=\"px-3 py-1 text-text-secondary hover:bg-gray-50 hover:text-text-main text-[11px] font-medium rounded transition-colors\">Year</button>
\t\t</div>
\t</div>

\t<div class=\"grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4\">
\t\t<div class=\"bg-card-bg p-5 rounded-xl border border-border-light hover:border-primary/50 transition-colors shadow-sm hover:shadow-md\">
\t\t\t<div class=\"flex justify-between items-start mb-4\">
\t\t\t\t<div class=\"p-2 rounded-lg bg-blue-50 text-primary\">
\t\t\t\t\t<span class=\"material-symbols-outlined\">payments</span>
\t\t\t\t</div>
\t\t\t\t<span class=\"flex items-center text-emerald-600 text-xs font-bold bg-emerald-50 px-2 py-1 rounded-full border border-emerald-100\">
\t\t\t\t\t<span class=\"material-symbols-outlined text-[14px] mr-1\">trending_up</span>+12%
\t\t\t\t</span>
\t\t\t</div>
\t\t\t<p class=\"text-text-secondary text-xs font-bold uppercase tracking-wider mb-1\">Total Revenue</p>
\t\t\t<h3 class=\"text-2xl font-black text-text-main tabular-nums\">\$45,231.89</h3>
\t\t</div>
\t\t<div class=\"bg-card-bg p-5 rounded-xl border border-border-light hover:border-primary/50 transition-colors shadow-sm hover:shadow-md\">
\t\t\t<div class=\"flex justify-between items-start mb-4\">
\t\t\t\t<div class=\"p-2 rounded-lg bg-purple-50 text-purple-600\">
\t\t\t\t\t<span class=\"material-symbols-outlined\">groups</span>
\t\t\t\t</div>
\t\t\t\t<span class=\"flex items-center text-emerald-600 text-xs font-bold bg-emerald-50 px-2 py-1 rounded-full border border-emerald-100\">
\t\t\t\t\t<span class=\"material-symbols-outlined text-[14px] mr-1\">trending_up</span>+5.2%
\t\t\t\t</span>
\t\t\t</div>
\t\t\t<p class=\"text-text-secondary text-xs font-bold uppercase tracking-wider mb-1\">Active Sessions</p>
\t\t\t<h3 class=\"text-2xl font-black text-text-main tabular-nums\">1,204</h3>
\t\t</div>
\t\t<div class=\"bg-card-bg p-5 rounded-xl border border-border-light hover:border-primary/50 transition-colors shadow-sm hover:shadow-md\">
\t\t\t<div class=\"flex justify-between items-start mb-4\">
\t\t\t\t<div class=\"p-2 rounded-lg bg-orange-50 text-orange-600\">
\t\t\t\t\t<span class=\"material-symbols-outlined\">output</span>
\t\t\t\t</div>
\t\t\t\t<span class=\"flex items-center text-red-600 text-xs font-bold bg-red-50 px-2 py-1 rounded-full border border-red-100\">
\t\t\t\t\t<span class=\"material-symbols-outlined text-[14px] mr-1\">trending_down</span>-2.1%
\t\t\t\t</span>
\t\t\t</div>
\t\t\t<p class=\"text-text-secondary text-xs font-bold uppercase tracking-wider mb-1\">PHP Version</p>
\t\t\t<h3 class=\"text-2xl font-black text-text-main tabular-nums\">{{ constant('PHP_VERSION') }}</h3>
\t\t</div>
\t\t<div class=\"bg-card-bg p-5 rounded-xl border border-border-light hover:border-primary/50 transition-colors shadow-sm hover:shadow-md\">
\t\t\t<div class=\"flex justify-between items-start mb-4\">
\t\t\t\t<div class=\"p-2 rounded-lg bg-pink-50 text-pink-600\">
\t\t\t\t\t<span class=\"material-symbols-outlined\">schedule</span>
\t\t\t\t</div>
\t\t\t\t<span class=\"flex items-center text-emerald-600 text-xs font-bold bg-emerald-50 px-2 py-1 rounded-full border border-emerald-100\">
\t\t\t\t\t<span class=\"material-symbols-outlined text-[14px] mr-1\">trending_up</span>+10s
\t\t\t\t</span>
\t\t\t</div>
\t\t\t<p class=\"text-text-secondary text-xs font-bold uppercase tracking-wider mb-1\">Admin Path</p>
\t\t\t<h3 class=\"text-lg font-black text-text-main\">{{ admin_path }}</h3>
\t\t</div>
\t</div>

\t<div class=\"grid grid-cols-1 lg:grid-cols-3 gap-6\">
\t\t<div class=\"lg:col-span-2 bg-card-bg rounded-xl border border-border-light p-6 shadow-sm\">
\t\t\t<div class=\"flex items-center justify-between mb-6\">
\t\t\t\t<div>
\t\t\t\t\t<h3 class=\"text-text-main font-bold text-lg\">Traffic Overview</h3>
\t\t\t\t\t<p class=\"text-text-secondary text-sm font-normal\">Comparison with previous period</p>
\t\t\t\t</div>
\t\t\t\t<button class=\"text-primary hover:text-primary-hover text-xs font-bold flex items-center gap-1 transition-colors uppercase tracking-wider\">
\t\t\t\t\tDownload Report
\t\t\t\t\t<span class=\"material-symbols-outlined text-[16px]\">download</span>
\t\t\t\t</button>
\t\t\t</div>
\t\t\t<div class=\"relative h-[280px] w-full mt-4\">
\t\t\t\t<div class=\"absolute inset-0 flex flex-col justify-between text-text-secondary/60 text-[10px] font-bold\">
\t\t\t\t\t<div class=\"w-full border-b border-dashed border-gray-100 pb-1\">50K</div>
\t\t\t\t\t<div class=\"w-full border-b border-dashed border-gray-100 pb-1\">40K</div>
\t\t\t\t\t<div class=\"w-full border-b border-dashed border-gray-100 pb-1\">30K</div>
\t\t\t\t\t<div class=\"w-full border-b border-dashed border-gray-100 pb-1\">20K</div>
\t\t\t\t\t<div class=\"w-full border-b border-dashed border-gray-100 pb-1\">10K</div>
\t\t\t\t\t<div class=\"w-full border-b border-dashed border-gray-100 pb-1\">0</div>
\t\t\t\t</div>
\t\t\t\t<div class=\"absolute inset-0 pl-8 pb-6 flex items-end justify-between pt-4\">
\t\t\t\t\t<div class=\"absolute bottom-6 left-8 right-0 top-4 overflow-hidden\">
\t\t\t\t\t\t<svg class=\"w-full h-full\" preserveaspectratio=\"none\" viewbox=\"0 0 100 50\">
\t\t\t\t\t\t\t<path d=\"M0,45 C10,40 20,42 30,35 C40,28 50,32 60,25 C70,18 80,22 90,15 L100,10\" fill=\"none\" stroke=\"#cbd5e1\" stroke-dasharray=\"2,1\" stroke-width=\"0.5\"></path>
\t\t\t\t\t\t\t<defs>
\t\t\t\t\t\t\t\t<linearGradient id=\"gradientBlue\" x1=\"0\" x2=\"0\" y1=\"0\" y2=\"1\">
\t\t\t\t\t\t\t\t\t<stop offset=\"0%\" stop-color=\"#135bec\" stop-opacity=\"0.2\"></stop>
\t\t\t\t\t\t\t\t\t<stop offset=\"100%\" stop-color=\"#135bec\" stop-opacity=\"0\"></stop>
\t\t\t\t\t\t\t\t</linearGradient>
\t\t\t\t\t\t\t</defs>
\t\t\t\t\t\t\t<path d=\"M0,35 C10,38 20,30 30,25 C40,20 50,22 60,15 C70,8 80,12 90,5 L100,2 V50 H0 Z\" fill=\"url(#gradientBlue)\"></path>
\t\t\t\t\t\t\t<path d=\"M0,35 C10,38 20,30 30,25 C40,20 50,22 60,15 C70,8 80,12 90,5 L100,2\" fill=\"none\" stroke=\"#135bec\" stroke-width=\"0.8\"></path>
\t\t\t\t\t\t\t<circle cx=\"60\" cy=\"15\" fill=\"#ffffff\" r=\"1.5\" stroke=\"#135bec\" stroke-width=\"1\"></circle>
\t\t\t\t\t\t</svg>
\t\t\t\t\t\t<div class=\"absolute top-[20%] left-[58%] bg-white text-text-main text-[10px] px-2 py-1 rounded shadow-lg border border-border-light whitespace-nowrap z-10 font-bold\">
\t\t\t\t\t\t\t<div>2,450 Visits</div>
\t\t\t\t\t\t\t<div class=\"text-text-secondary font-medium\">Nov 14</div>
\t\t\t\t\t\t</div>
\t\t\t\t\t</div>
\t\t\t\t</div>
\t\t\t</div>
\t\t\t<div class=\"flex justify-between pl-8 text-[10px] text-text-secondary font-bold mt-2\">
\t\t\t\t<span>NOV 01</span>
\t\t\t\t<span>NOV 05</span>
\t\t\t\t<span>NOV 10</span>
\t\t\t\t<span>NOV 15</span>
\t\t\t\t<span>NOV 20</span>
\t\t\t\t<span>NOV 25</span>
\t\t\t\t<span>NOV 30</span>
\t\t\t</div>
\t\t</div>
\t\t<div class=\"lg:col-span-1 flex flex-col gap-6\">
\t\t\t<div class=\"bg-card-bg rounded-xl border border-border-light p-6 flex flex-col gap-4 shadow-sm flex-1\">
\t\t\t\t<h3 class=\"text-text-main font-bold text-lg\">Monthly Target</h3>
\t\t\t\t<div class=\"relative size-36 mx-auto my-2\">
\t\t\t\t\t<svg class=\"size-full -rotate-90\" viewbox=\"0 0 36 36\">
\t\t\t\t\t\t<path class=\"text-gray-100\" d=\"M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"3\"></path>
\t\t\t\t\t\t<path class=\"text-primary\" d=\"M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831\" fill=\"none\" stroke=\"currentColor\" stroke-dasharray=\"75, 100\" stroke-linecap=\"round\" stroke-width=\"3\"></path>
\t\t\t\t\t</svg>
\t\t\t\t\t<div class=\"absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 text-center\">
\t\t\t\t\t\t<span class=\"text-2xl font-black text-text-main tabular-nums\">75%</span>
\t\t\t\t\t\t<span class=\"block text-[9px] text-text-secondary font-black tracking-widest uppercase\">Achieved</span>
\t\t\t\t\t</div>
\t\t\t\t</div>
\t\t\t\t<div class=\"mt-auto space-y-3\">
\t\t\t\t\t<div class=\"flex justify-between text-xs\">
\t\t\t\t\t\t<span class=\"text-text-secondary font-medium\">Monthly Target</span>
\t\t\t\t\t\t<span class=\"text-text-main font-bold tabular-nums\">\$60,000.00</span>
\t\t\t\t\t</div>
\t\t\t\t\t<div class=\"flex justify-between text-xs\">
\t\t\t\t\t\t<span class=\"text-text-secondary font-medium\">Current Revenue</span>
\t\t\t\t\t\t<span class=\"text-text-main font-bold tabular-nums\">\$45,231.89</span>
\t\t\t\t\t</div>
\t\t\t\t</div>
\t\t\t</div>
\t\t</div>
\t</div>

\t<div class=\"bg-card-bg rounded-xl border border-border-light overflow-hidden shadow-sm mb-10\">
\t\t<div class=\"px-6 py-4 border-b border-border-light flex items-center justify-between\">
\t\t\t<h3 class=\"text-text-main font-bold text-lg\">Recent Transactions</h3>
\t\t\t<div class=\"flex gap-2\">
\t\t\t\t<button class=\"flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-border-light bg-white text-xs font-bold text-text-secondary hover:text-primary hover:border-primary transition-colors shadow-sm\">
\t\t\t\t\t<span class=\"material-symbols-outlined text-[16px]\">filter_list</span>
\t\t\t\t\tFILTER
\t\t\t\t</button>
\t\t\t\t<button class=\"flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-border-light bg-white text-xs font-bold text-text-secondary hover:text-primary hover:border-primary transition-colors shadow-sm\">
\t\t\t\t\t<span class=\"material-symbols-outlined text-[16px]\">download</span>
\t\t\t\t\tEXPORT
\t\t\t\t</button>
\t\t\t</div>
\t\t</div>
\t\t<div class=\"overflow-x-auto\">
\t\t\t<table class=\"w-full text-left text-sm border-collapse\">
\t\t\t\t<thead class=\"bg-gray-50 text-text-secondary border-b border-border-light\">
\t\t\t\t\t<tr>
\t\t\t\t\t\t<th class=\"px-6 py-3 font-bold text-xs uppercase tracking-wider\">Transaction ID</th>
\t\t\t\t\t\t<th class=\"px-6 py-3 font-bold text-xs uppercase tracking-wider\">User Account</th>
\t\t\t\t\t\t<th class=\"px-6 py-3 font-bold text-xs uppercase tracking-wider\">Amount</th>
\t\t\t\t\t\t<th class=\"px-6 py-3 font-bold text-xs uppercase tracking-wider\">Status</th>
\t\t\t\t\t\t<th class=\"px-6 py-3 font-bold text-xs uppercase tracking-wider\">Processed Date</th>
\t\t\t\t\t\t<th class=\"px-6 py-3 font-bold text-xs uppercase tracking-wider text-right\">Action</th>
\t\t\t\t\t</tr>
\t\t\t\t</thead>
\t\t\t\t<tbody class=\"divide-y divide-border-light text-text-main\">
\t\t\t\t\t<tr class=\"hover:bg-gray-50 transition-colors group\">
\t\t\t\t\t\t<td class=\"px-6 py-4 font-mono text-xs text-text-secondary font-medium\">#TRX-9821</td>
\t\t\t\t\t\t<td class=\"px-6 py-4\">
\t\t\t\t\t\t\t<div class=\"flex items-center gap-3\">
\t\t\t\t\t\t\t\t<div class=\"size-8 rounded-full bg-cover bg-center shadow-sm border border-gray-100\" style='background-image: url(\"https://lh3.googleusercontent.com/aida-public/AB6AXuCV6iIzeMGh2UPiS-yaqEdLXTHsdEoO-Wh9Z7S1TOh22HAbeOT2v5cLH9Fcp_UvrqFfd8j7pYJMVtB2IlWWlOe3oncBvR7kjjYYNFRLbcKktNjSrl6Tf9QmSeS2wZHoNHQ04LL9XnTbVu5a9YNmTtlHpWCjA8ZHBVBU8yzHvMj8l_PwIcpGUsHyTUZ3AMt47vgrClmutyptzyJ14wUWXggUjbcv4NWRmETrEUMcbdlZFs7_ty8VbI22bKd74ft3sjOTVgky_fzpKUqv\");'></div>
\t\t\t\t\t\t\t\t<div>
\t\t\t\t\t\t\t\t\t<p class=\"font-bold text-text-main text-sm\">Courtney Henry</p>
\t\t\t\t\t\t\t\t\t<p class=\"text-[10px] text-text-secondary font-medium uppercase tracking-tight\">Pro Member</p>
\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t</td>
\t\t\t\t\t\t<td class=\"px-6 py-4 font-bold text-text-main tabular-nums\">\$1,290.00</td>
\t\t\t\t\t\t<td class=\"px-6 py-4\">
\t\t\t\t\t\t\t<span class=\"inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-emerald-50 text-emerald-600 border border-emerald-100 tracking-wide\">
\t\t\t\t\t\t\t\t<span class=\"size-1 rounded-full bg-emerald-500\"></span>
\t\t\t\t\t\t\t\tPaid
\t\t\t\t\t\t\t</span>
\t\t\t\t\t\t</td>
\t\t\t\t\t\t<td class=\"px-6 py-4 text-text-secondary text-xs font-medium\">Nov 24, 2023</td>
\t\t\t\t\t\t<td class=\"px-6 py-4 text-right\">
\t\t\t\t\t\t\t<button class=\"text-text-secondary hover:text-primary p-1 rounded hover:bg-white transition-colors\">
\t\t\t\t\t\t\t\t<span class=\"material-symbols-outlined text-[18px]\">more_horiz</span>
\t\t\t\t\t\t\t</button>
\t\t\t\t\t\t</td>
\t\t\t\t\t</tr>
\t\t\t\t\t<tr class=\"hover:bg-gray-50 transition-colors group\">
\t\t\t\t\t\t<td class=\"px-6 py-4 font-mono text-xs text-text-secondary font-medium\">#TRX-9822</td>
\t\t\t\t\t\t<td class=\"px-6 py-4\">
\t\t\t\t\t\t\t<div class=\"flex items-center gap-3\">
\t\t\t\t\t\t\t\t<div class=\"size-8 rounded-full bg-cover bg-center shadow-sm border border-gray-100\" style='background-image: url(\"https://lh3.googleusercontent.com/aida-public/AB6AXuBFPjZVYEkCshBh_jI1qVqxhvugHlOWOOM9INapjSoaUEa7RnuIt5hkyjNkrsI55KvVMWRqUBtL1MFVBxMeK2Z7myyFansC58C0FI7QI3ODLJT9tHrSNLvgCD26I_Dm2DZkCuoSpPwU6sIplwYqcOwHsfh4d4vsmDMS7jfEfvMPfC-0ptXEofwhC_pHam_sVp-kXNMECtKMlnFWzapqVU0OAtZ4qaOnCAoyebrPh7Wt2hnt0SWz0RpI8dq8b35D3eZr2OBFtNp_OZc_\");'></div>
\t\t\t\t\t\t\t\t<div>
\t\t\t\t\t\t\t\t\t<p class=\"font-bold text-text-main text-sm\">Darrell Steward</p>
\t\t\t\t\t\t\t\t\t<p class=\"text-[10px] text-text-secondary font-medium uppercase tracking-tight\">Free Member</p>
\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t</td>
\t\t\t\t\t\t<td class=\"px-6 py-4 font-bold text-text-main tabular-nums\">\$59.00</td>
\t\t\t\t\t\t<td class=\"px-6 py-4\">
\t\t\t\t\t\t\t<span class=\"inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-yellow-50 text-yellow-600 border border-yellow-100 tracking-wide\">
\t\t\t\t\t\t\t\t<span class=\"size-1 rounded-full bg-yellow-500\"></span>
\t\t\t\t\t\t\t\tPending
\t\t\t\t\t\t\t</span>
\t\t\t\t\t\t</td>
\t\t\t\t\t\t<td class=\"px-6 py-4 text-text-secondary text-xs font-medium\">Nov 24, 2023</td>
\t\t\t\t\t\t<td class=\"px-6 py-4 text-right\">
\t\t\t\t\t\t\t<button class=\"text-text-secondary hover:text-primary p-1 rounded hover:bg-white transition-colors\">
\t\t\t\t\t\t\t\t<span class=\"material-symbols-outlined text-[18px]\">more_horiz</span>
\t\t\t\t\t\t\t</button>
\t\t\t\t\t\t</td>
\t\t\t\t\t</tr>
\t\t\t\t</tbody>
\t\t\t</table>
\t\t</div>
\t\t<div class=\"px-6 py-3 border-t border-border-light flex items-center justify-between text-[11px] text-text-secondary bg-gray-50/50\">
\t\t\t<span class=\"font-medium\">SHOWING
\t\t\t\t<span class=\"text-text-main font-bold\">2</span>
\t\t\t\tOF
\t\t\t\t<span class=\"text-text-main font-bold\">128</span>
\t\t\t\tTRANSACTIONS</span>
\t\t\t<div class=\"flex gap-2\">
\t\t\t\t<button class=\"px-3 py-1 rounded border border-border-light hover:bg-white hover:text-primary transition-colors bg-white shadow-sm font-bold uppercase tracking-tight\">Previous</button>
\t\t\t\t<button class=\"px-3 py-1 rounded border border-border-light hover:bg-white hover:text-primary transition-colors bg-white shadow-sm font-bold uppercase tracking-tight\">Next</button>
\t\t\t</div>
\t\t</div>
\t</div>
{% endblock %}
", "admin/index.html.twig", "/home/antoine/Workspace/htdocs/mta-cms/templates/admin/index.html.twig");
    }
}
