<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * @package local_vflibs
 * @author valery.fremaux@gmail.com
 * @category local
 */
defined('MOODLE_INTERNAL') || die();

class local_vflibs_renderer extends plugin_renderer_base {

    // JQWidget wrappers.
    /*
     * Properties : max, width, height
     */
    public function jqw_bargauge_simple($name, $data, $properties = null, $labels = array()) {

        $str = '';

        if (empty($properties['max'])) {
            $properties['max'] = 100;
        }
        if (empty($properties['width'])) {
            $properties['width'] = 500;
        }
        if (empty($properties['height'])) {
            $properties['height'] = 500;
        }
        if (empty($properties['cropwidth'])) {
            $properties['cropwidth'] = 300;
        }
        if (empty($properties['cropheight'])) {
            $properties['cropheight'] = 300;
        }
        if (!empty($properties['crop'])) {
            $properties['cropheight'] = 300;
            $properties['cropwidth'] = $properties['crop'];
        }
        if (empty($properties['animationduration'])) {
            $properties['animationduration'] = 500;
        }

        $str .= '';

        $str .= '<script type="text/javascript">';
        $str .= '    $(document).ready(function ()';
        $str .= '    {';
        $str .= '        $(\'#'.$name.'\').jqxBarGauge({colorScheme: "scheme02", width: ';
        $str .= $properties['width'].', height:' .$properties['height'].',';
        if (array_key_exists('title', $properties)) {
            $str .= '            title: { text: \''.$properties['title'].'\', ';
        }
        if (array_key_exists('subtitle', $properties)) {
            $str .= '            subtitle: \''.$properties['subtitle'].'\') },';
        }
        $str .= '            values: ['.implode(', ', $data).'], max: '.$properties['max'].', tooltip: {';
        $str .= '                visible: true, formatFunction: function (value)';
        $str .= '                {';
        $str .= '                    var realVal = parseInt(value);';
        $str .= '                    return (realVal);';
        $str .= '                },';
        $str .= '            },';
        $str .= '            animationDuration: '.$properties['animationduration'];
        $str .= '        });';
        $str .= '    });';
        $str .= '</script>';

        $w = $properties['cropwidth'];
        $h = $properties['cropheight'];
        $l = round(($properties['cropwidth'] - $properties['width']) / 2);
        $t = round(($properties['cropheight'] - $properties['height']) / 2);
        $str .= '<div class="jqw-gauge-container" style="width:'.$w.'px;height:'.$h.'px;">';
        $str .= '<div id="'.$name.'" style="overflow:hidden;position:relative;top:'.$t.'px;left:'.$l.'px"></div>';
        $str .= '</div>';

        return $str;
    }

    public function jqw_progress_bar($name, $value, $properties = array()) {

        if (empty($properties['animation'])) {
            $properties['animation'] = 0;
        }
        if (empty($properties['width'])) {
            $properties['width'] = 150;
        }
        if (empty($properties['height'])) {
            $properties['height'] = 24;
        }
        if (empty($properties['template'])) {
            $properties['template'] = 'primary';
        }

        $str = '';
        $str .= '<script type="text/javascript">';
        $str .= '    $(document).ready(function ()';
        $str .= '    {';
        $str .= '       $("#'.$name.'").jqxProgressBar({';
        $str .= '           width: '.$properties['width'].',';
        $str .= '           height: '.$properties['height'].',';
        $str .= '           value: '.$value.',';
        $str .= '           template: \''.$properties['template'].'\',';
        $str .= '           animationDuration: '.$properties['animation'].'});';
        $str .= '    });';
        $str .= '</script>';
        $str .= '<div style="margin-top: 10px; overflow: hidden;" id="'.$name.'"></div>';

        return $str;
    }

    /**
     * @param string $name
     * @param array $properties array with ('width', 'height', 'desc', 'barsize', 'tooltip') keys
     * @param array $ranges an array of range objects having ('start', 'end', 'color', 'opacity') keys
     * @param object $pointer an object with ('value', 'label', 'size', 'color') keys
     * @param object $target an object with ('value', 'label', 'size', 'color') keys
     * @param object $ticks an object with ('position', 'interval', 'size') keys
     */
    public function jqw_bulletchart($name, $properties, $ranges, $pointer, $target, $ticks = null) {

        if (is_null($ticks)) {
            $ticks = new Stdclass;
            $ticks->position = 'both';
            $ticks->interval = 50;
            $ticks->size = 10;
        }

        if (!isset($properties['barsize'])) {
            $properties['barsize'] = 20;
        }

        if (!isset($properties['bgcolor'])) {
            $properties['bgcolor'] = '#e0e0e0';
        }

        if (!isset($properties['bgopacity'])) {
            $properties['bgopacity'] = 1;
        }

        if (empty($ranges)) {
            $ranges = array();
            $defaultrange = (object) array('start' => 0,
                                           'end' => 100,
                                           'color' => $properties['bgcolor'],
                                           'opacity' => $properties['bgopacity']);
            $ranges[] = $defaultrange;
        }

        if (empty($pointer)) {
            if (!isset($pointer->size)) {
                $pointer->size = 80;
            }
            if (!isset($pointer->color)) {
                $pointer->color = '#000000';
            }
        }

        if (empty($target)) {
            if (!isset($target->size)) {
                $target->size = 80;
            }
            if (!isset($target->color)) {
                $target->color = '#000000';
            }
        }

        if (!array_key_exists('tooltip', $properties)) {
            $properties['tooltip'] = 'true';
        }

        $str = '';

        $str .= '<div id="jqxBulletChart'.$properties['id'].'" class="jqwidgets-bulletchart"></div>'."\n";
        $str .= '<script type="text/javascript">';
        $str .= '    $(document).ready(function () {'."\n";
        $str .= '        $("#jqxBulletChart'.$properties['id'].'").jqxBulletChart({'."\n";
        $str .= '            width: '.$properties['width'].', '."\n";
        $str .= '            height: '.$properties['height'].', '."\n";
        $str .= '            barSize: "'.$properties['barsize'].'%", '."\n";
        $str .= '            title: "'.$name.'",'."\n";
        if (!empty($properties['desc'])) {
            $str .= '            description: "'.$properties['desc'].'",'."\n";
        } else {
            $str .= '            description: "",'."\n";
        }

        if (!empty($ranges)) {
            $str .= '            ranges: [';
            foreach ($ranges as $r) {
                if (empty($r->opacity)) {
                    $r->opacity = '1';
                }
                $rangestr = '    { startValue: '.(0 + $r->start).', endValue: '.(0 + $r->end).', color: "'.$r->color;
                $rangestr .= '", opacity: '.$r->opacity.'} '."\n";
                $rangearr[] = $rangestr;
            }
            $str .= implode(',', $rangearr);
            $str .= '    ],'."\n";
        }

        if (!empty($pointer)) {
            $str .= 'pointer: { value: '.(0 + $pointer->value).', label: "'.$pointer->label.'", size: "'.$pointer->size;
            $str .= '%", color: "'.$pointer->color.'" },'."\n";
        }
        if (!empty($target)) {
            $str .= 'target: { value: '.(0 + $target->value).', label: "'.$target->label.'", size: '.$target->size;
            $str .= ', color: "'.$target->color.'" },'."\n";
        }

        $str .= 'ticks: { position: "'.$ticks->position.'", interval: '.$ticks->interval.', size: '.$ticks->size.' },'."\n";
        $str .= 'labelsFormat: "'.$properties['ticklabelformat'].'",'."\n";
        $str .= 'showTooltip: '.$properties['tooltip']."\n";
        $str .= '    });'."\n";
        $str .= '});'."\n";
        $str .= '</script>'."\n";

        return $str;
    }

    /**
     * Data is expected as an array of objects, objects have fields mapped to char series.
     * @param string $name the graph title
     * @param array $data an array of source data, as an array of object containing one member per serie
     * @param array $properies a bag with keyed properties to serve graph parametrization
     * @param string $component the component name where strings come from.
     */
    public function jqw_bar_chart($name, $data, $properties, $component) {

        if (empty($data)) {
            return '';
        }

        if (empty($properties['direction'])) {
            $properties['direction'] = 'vertical';
        }

        if (empty($properties['xflip'])) {
            $properties['xflip'] = 'false';
        }

        if (empty($properties['yflip'])) {
            $properties['yflip'] = 'false';
        }

        // Guess series from first record.
        $firstarr = (array)$data[0];
        $series = array_keys($firstarr);
        $xaxis = array_shift($series);

        // Get other series and convert to a jsonified string.
        $seriestack = array();
        if (!empty($series)) {
            foreach ($series as $s) {
                $serieobj = new StdClass();
                $serieobj->dataField = $s;
                $serieobj->displayText = get_string($s, $component);
                $seriestack[] = $serieobj;
            }
        }
        $seriesarr = json_encode($seriestack);

        $padding = '{ left: 20, top: 5, right: 20, bottom: 5 }';
        if (!empty($properties['padding'])) {
            $padding = json_encode($properties['padding']);
        }

        $titlepadding = '{ left: 90, top: 0, right: 0, bottom: 10 }';
        if (!empty($properties['titlepadding'])) {
            $titlepadding = json_encode($properties['titlepadding']);
        }

        $str = '';

        $str .= '<center>';
        $str .= '<div id="jqxBarChart'.$properties['id'].'"
                      class="vflibs-jqbarchart"
                      style="width:'.$properties['width'].'px; height:'.$properties['height'].'px"></div>'."\n";
        $str .= '</center>';
        $str .= '<script type="text/javascript">';

        $str .= '$(document).ready(function () {
            // prepare chart data
            var graphdata'.$properties['id'].' = '.json_encode($data).';

            // prepare jqxChart settings
            var settings'.$properties['id'].' = {
                title: "'.$name.'",
                description: "'.$properties['desc'].'",
                showLegend: true,
                enableAnimations: true,
                padding: '.$padding.',
                titlePadding: '.$titlepadding.',
                source: graphdata'.$properties['id'].',
                xAxis:
                {
                    dataField: \''.$xaxis.'\',
                    gridLines: { visible: true },
                    flip: '.$properties['xflip'].',
                    labels: {
                        visible: true,
                        angle:90
                    }
                },
                valueAxis:
                {
                    flip: '.$properties['yflip'].',
                    labels: {
                        visible: true,
                        angle:90
                    }
                },
                colorScheme: \'scheme01\',
                seriesGroups:
                    [
                        {
                            type: \'column\',
                            orientation: \''.$properties['direction'].'\',
                            columnsGapPercent: 50,
                            toolTipFormatSettings: { thousandsSeparator: \',\' },
                            series: '.$seriesarr.'
                        }
                    ]
            };
            // setup the chart
            $(\'#jqxBarChart'.$properties['id'].'\').jqxChart(settings'.$properties['id'].');
        });';
        $str .= '</script>';

        return $str;
    }
}