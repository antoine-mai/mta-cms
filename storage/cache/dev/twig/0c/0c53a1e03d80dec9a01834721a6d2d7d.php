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

/* admin.html.twig */
class __TwigTemplate_bd33cefd95f8bae2d9e2729c3fe00ad9 extends Template
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
            'title' => [$this, 'block_title'],
            'stylesheets' => [$this, 'block_stylesheets'],
            'body' => [$this, 'block_body'],
            'javascripts' => [$this, 'block_javascripts'],
        ];
    }

    protected function doDisplay(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "admin.html.twig"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "admin.html.twig"));

        // line 1
        yield "<!DOCTYPE html>
<html class=\"light\" lang=\"en\">
\t<head>
\t\t<meta charset=\"utf-8\"/>
\t\t<meta content=\"width=device-width, initial-scale=1.0\" name=\"viewport\"/>
\t\t<title>
\t\t\t";
        // line 7
        yield from $this->unwrap()->yieldBlock('title', $context, $blocks);
        // line 9
        yield "\t\t</title>
\t\t<script src=\"https://cdn.tailwindcss.com?plugins=forms,container-queries\"></script>
\t\t<link href=\"https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap\" rel=\"stylesheet\"/>
\t\t<link href=\"https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&amp;display=swap\" rel=\"stylesheet\"/>
\t\t<script id=\"tailwind-config\">
\t\t\ttailwind.config = {
darkMode: \"class\",
theme: {
extend: {
colors: {
\"primary\": \"#135bec\",
\"primary-hover\": \"#2563eb\",
\"background-main\": \"#ffffff\",
\"sidebar-slim\": \"#F8F9FA\",
\"sidebar-wide\": \"#F3F4F6\",
\"card-bg\": \"#ffffff\",
\"border-light\": \"#e2e8f0\",
\"text-main\": \"#1e293b\",
\"text-secondary\": \"#64748b\",
\"text-muted\": \"#94a3b8\"
},
fontFamily: {
\"sans\": [
\"Roboto\", \"sans-serif\"
],
\"display\": [\"Roboto\", \"sans-serif\"]
},
borderRadius: {
\"DEFAULT\": \"0.25rem\",
\"lg\": \"0.5rem\",
\"xl\": \"0.75rem\",
\"full\": \"9999px\"
}
}
}
}
\t\t</script>
\t\t<style type=\"text/tailwindcss\">
\t\t\tbody {
\t\t\t\t\t\t            font-family: 'Roboto', sans-serif;
\t\t\t\t\t\t        }
\t\t\t\t\t\t        ::-webkit-scrollbar {
\t\t\t\t\t\t            width: 6px;
\t\t\t\t\t\t            height: 6px;
\t\t\t\t\t\t        }
\t\t\t\t\t\t        ::-webkit-scrollbar-track {
\t\t\t\t\t\t            background: #f1f5f9; 
\t\t\t\t\t\t        }
\t\t\t\t\t\t        ::-webkit-scrollbar-thumb {
\t\t\t\t\t\t            background: #cbd5e1; 
\t\t\t\t\t\t            border-radius: 3px;
\t\t\t\t\t\t        }
\t\t\t\t\t\t        ::-webkit-scrollbar-thumb:hover {
\t\t\t\t\t\t            background: #94a3b8; 
\t\t\t\t\t\t        }
\t\t</style>
\t\t";
        // line 65
        yield from $this->unwrap()->yieldBlock('stylesheets', $context, $blocks);
        // line 66
        yield "\t</head>
\t<body class=\"bg-background-main text-text-main h-screen w-full flex overflow-hidden font-sans\">

\t\t";
        // line 69
        yield from $this->load("admin/components/_sidebar.html.twig", 69)->unwrap()->yield($context);
        // line 70
        yield "
\t\t<main class=\"flex-1 flex flex-col min-w-0 bg-background-main relative\">

\t\t\t";
        // line 73
        yield from $this->load("admin/components/_header.html.twig", 73)->unwrap()->yield($context);
        // line 74
        yield "
\t\t\t<div class=\"flex-1 overflow-y-auto p-6 lg:p-8 bg-background-main\">
\t\t\t\t<div class=\"max-w-7xl mx-auto flex flex-col gap-6\"> ";
        // line 76
        yield from $this->unwrap()->yieldBlock('body', $context, $blocks);
        // line 77
        yield "\t\t\t\t\t</div>
\t\t\t\t</div>
\t\t\t</main>
\t\t\t";
        // line 80
        yield from $this->unwrap()->yieldBlock('javascripts', $context, $blocks);
        // line 81
        yield "\t\t</body>
\t</html>
";
        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        yield from [];
    }

    // line 7
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

        yield "Admin Dashboard - Roboto Edition
\t\t\t";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        yield from [];
    }

    // line 65
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_stylesheets(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "stylesheets"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "stylesheets"));

        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        yield from [];
    }

    // line 76
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

        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        yield from [];
    }

    // line 80
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_javascripts(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "javascripts"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "javascripts"));

        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "admin.html.twig";
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
        return array (  230 => 80,  208 => 76,  186 => 65,  162 => 7,  149 => 81,  147 => 80,  142 => 77,  140 => 76,  136 => 74,  134 => 73,  129 => 70,  127 => 69,  122 => 66,  120 => 65,  62 => 9,  60 => 7,  52 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("<!DOCTYPE html>
<html class=\"light\" lang=\"en\">
\t<head>
\t\t<meta charset=\"utf-8\"/>
\t\t<meta content=\"width=device-width, initial-scale=1.0\" name=\"viewport\"/>
\t\t<title>
\t\t\t{% block title %}Admin Dashboard - Roboto Edition
\t\t\t{% endblock %}
\t\t</title>
\t\t<script src=\"https://cdn.tailwindcss.com?plugins=forms,container-queries\"></script>
\t\t<link href=\"https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap\" rel=\"stylesheet\"/>
\t\t<link href=\"https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&amp;display=swap\" rel=\"stylesheet\"/>
\t\t<script id=\"tailwind-config\">
\t\t\ttailwind.config = {
darkMode: \"class\",
theme: {
extend: {
colors: {
\"primary\": \"#135bec\",
\"primary-hover\": \"#2563eb\",
\"background-main\": \"#ffffff\",
\"sidebar-slim\": \"#F8F9FA\",
\"sidebar-wide\": \"#F3F4F6\",
\"card-bg\": \"#ffffff\",
\"border-light\": \"#e2e8f0\",
\"text-main\": \"#1e293b\",
\"text-secondary\": \"#64748b\",
\"text-muted\": \"#94a3b8\"
},
fontFamily: {
\"sans\": [
\"Roboto\", \"sans-serif\"
],
\"display\": [\"Roboto\", \"sans-serif\"]
},
borderRadius: {
\"DEFAULT\": \"0.25rem\",
\"lg\": \"0.5rem\",
\"xl\": \"0.75rem\",
\"full\": \"9999px\"
}
}
}
}
\t\t</script>
\t\t<style type=\"text/tailwindcss\">
\t\t\tbody {
\t\t\t\t\t\t            font-family: 'Roboto', sans-serif;
\t\t\t\t\t\t        }
\t\t\t\t\t\t        ::-webkit-scrollbar {
\t\t\t\t\t\t            width: 6px;
\t\t\t\t\t\t            height: 6px;
\t\t\t\t\t\t        }
\t\t\t\t\t\t        ::-webkit-scrollbar-track {
\t\t\t\t\t\t            background: #f1f5f9; 
\t\t\t\t\t\t        }
\t\t\t\t\t\t        ::-webkit-scrollbar-thumb {
\t\t\t\t\t\t            background: #cbd5e1; 
\t\t\t\t\t\t            border-radius: 3px;
\t\t\t\t\t\t        }
\t\t\t\t\t\t        ::-webkit-scrollbar-thumb:hover {
\t\t\t\t\t\t            background: #94a3b8; 
\t\t\t\t\t\t        }
\t\t</style>
\t\t{% block stylesheets %}{% endblock %}
\t</head>
\t<body class=\"bg-background-main text-text-main h-screen w-full flex overflow-hidden font-sans\">

\t\t{% include 'admin/components/_sidebar.html.twig' %}

\t\t<main class=\"flex-1 flex flex-col min-w-0 bg-background-main relative\">

\t\t\t{% include 'admin/components/_header.html.twig' %}

\t\t\t<div class=\"flex-1 overflow-y-auto p-6 lg:p-8 bg-background-main\">
\t\t\t\t<div class=\"max-w-7xl mx-auto flex flex-col gap-6\"> {% block body %}{% endblock %}
\t\t\t\t\t</div>
\t\t\t\t</div>
\t\t\t</main>
\t\t\t{% block javascripts %}{% endblock %}
\t\t</body>
\t</html>
", "admin.html.twig", "/home/antoine/Workspace/htdocs/mta-cms/templates/admin.html.twig");
    }
}
