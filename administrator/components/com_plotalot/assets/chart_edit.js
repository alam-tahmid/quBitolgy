function plotalot_fields(chart_type)    // customise the chart editor for the specific chart type
{
    disable_by_class('pjd_all');        // disable all fields with class 'pjd_all'
    hide_by_class('pjh_all');           // hide all fields with class 'pjh_all'
    switch(parseInt(chart_type))
    {
    case 100: // line
    case 110: // area
        enable_by_class('pjd_num_plots');
        show_by_class('pjh_legend_type','inline');
        show_by_class('pjh_show_grid','inline');
        enable_by_class('pjd_chart_title');
        enable_by_class('pjd_xy_titles');
        enable_by_class('pjd_x_params');
        show_by_class('pjh_x_format','inline');
        enable_by_class('pjd_y_params');
        enable_by_class('pjd_y_labels');
        show_by_class('pjh_y_format','inline');
        show_by_class('pjh_plot_style_line','table-row');
        break;
    case 200: // scatter
        enable_by_class('pjd_num_plots');
        show_by_class('pjh_legend_type','inline');
        show_by_class('pjh_show_grid','inline');
        enable_by_class('pjd_chart_title');
        enable_by_class('pjd_xy_titles');
        enable_by_class('pjd_x_params');
        show_by_class('pjh_x_format','inline');
        enable_by_class('pjd_y_params');
        enable_by_class('pjd_y_labels');
        show_by_class('pjh_y_format','inline');
        break;
    case 300: // bar
    case 310:
    case 320:
    case 330:
        enable_by_class('pjd_num_plots');
        show_by_class('pjh_legend_type','inline');
        show_by_class('pjh_chart_option_bar','inline');
        show_by_class('pjh_show_grid','inline');
        enable_by_class('pjd_chart_title');
        enable_by_class('pjd_xy_titles');
        enable_by_class('pjd_x_params');
        show_by_class('pjh_x_format','inline');
        enable_by_class('pjd_y_params');
        enable_by_class('pjd_y_labels');
        show_by_class('pjh_y_format','inline');
        break;
    case 400: // pie
    case 410:
    case 420:
    case 430:
        show_by_class('pjh_legend_type','inline');
        show_by_class('pjh_chart_option_pie','inline');
        enable_by_class('pjd_chart_title');
        show_by_class('pjh_plot_style_pie','table-row');
        break;
    case 500: // gauge
        enable_by_class('pjd_y_params');
        break;
    case 520: // timeline
        break;
    case 530: // bubble
        show_by_class('pjh_legend_type','inline');
        show_by_class('pjh_show_grid','inline');
        enable_by_class('pjd_chart_title');
        enable_by_class('pjd_xy_titles');
        enable_by_class('pjd_x_params');
        show_by_class('pjh_x_format','inline');
        enable_by_class('pjd_y_params');
        enable_by_class('pjd_y_labels');
        show_by_class('pjh_y_format','inline');
        break;
    case 540: // combo
    case 550:
        enable_by_class('pjd_num_plots');
        show_by_class('pjh_legend_type','inline');
        show_by_class('pjh_chart_option_bar','inline');
        show_by_class('pjh_show_grid','inline');
        enable_by_class('pjd_chart_title');
        enable_by_class('pjd_xy_titles');
        enable_by_class('pjd_x_params');
        show_by_class('pjh_x_format','inline');
        enable_by_class('pjd_y_params');
        enable_by_class('pjd_y_labels');
        show_by_class('pjh_y_format','inline');
        show_by_class('pjh_plot_type','table-row');
        break;
    }
}

function hide_by_class(classname) 
{
    var elements = document.getElementsByClassName(classname);
    for (i=0; i < elements.length; i++)
        elements[i].style.display = 'none';
}

function show_by_class(classname, style)
{
    var elements = document.getElementsByClassName(classname);
    for (i=0; i < elements.length; i++)
        elements[i].style.display = style;
}

function disable_by_class(classname) 
{
    var elements = document.getElementsByClassName(classname);
    for (i=0; i < elements.length; i++)
        elements[i].disabled=true;
}

function enable_by_class(classname) 
{
    var elements = document.getElementsByClassName(classname);
    for (i=0; i < elements.length; i++)
        elements[i].disabled=false;
}

