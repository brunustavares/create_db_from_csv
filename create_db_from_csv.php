<?php
/**
 * PHP script to create a Moodle database activity from a CSV file
 * (developed for UAb - Universidade Aberta)
 *
 * @category   PHP_Script
 * @package    create_db_from_csv
 * @author     Bruno Tavares <brunustavares@gmail.com>
 * @link       https://www.linkedin.com/in/brunomastavares/
 * @copyright  Copyright (C) 2026-present Bruno Tavares
 * @license    GNU General Public License v3 or later
 *             https://www.gnu.org/licenses/gpl-3.0.html
 * @version    2026032609
 * @date       2026-03-26
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

define('CLI_SCRIPT', true);

require(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/course/modlib.php');
require_once($CFG->dirroot . '/mod/data/lib.php');
require_once($CFG->libdir . '/clilib.php');

list($options, $unrecognized) = cli_get_params([
    'courseid' => null,
    'csv'      => null,
    'name'     => 'New Database',
    'help'     => false
]);

if ($options['help'] || !$options['courseid'] || !$options['csv']) {
    echo "\nmodo de uso:\n";
    echo "php create_db_from_csv.php --courseid=ID --csv=/path/file.csv [--name=\"nome_da_BD\"]\n";
    exit(1);
}

$courseid = (int)$options['courseid'];
$csvpath  = $options['csv'];
$dbname   = $options['name'];

if (!file_exists($csvpath)) {
    cli_error("\nficheiro CSV não encontrado: {$csvpath}");
}

$course = get_course($courseid);

echo "\na criar atividade '{$dbname}' no curso '{$course->fullname}'...\n";

global $DB;

// Criar atividade Database
$module = $DB->get_record('modules', ['name' => 'data'], '*', MUST_EXIST);

$moduleinfo = new stdClass();
$moduleinfo->module = $module->id;
$moduleinfo->modulename = 'data';
$moduleinfo->course = $courseid;
$moduleinfo->section = 0;
$moduleinfo->name = $dbname;
$moduleinfo->intro = 'criada automaticamente a partir de um ficheiro CSV';
$moduleinfo->introformat = FORMAT_HTML;
$moduleinfo->visible = 1;
$moduleinfo->comments = 0;
$moduleinfo->completion = 0;

$moduleinfo = add_moduleinfo($moduleinfo, $course);
$dataid = $moduleinfo->instance;

echo "atividade criada (ID: $dataid)\n\n";

// Ler o cabeçalho do CSV
$fp = fopen($csvpath, 'r');
$header = fgetcsv($fp);
fclose($fp);

if (!$header) {
    cli_error("\ncabeçalho CSV não encontrado ou vazio");
}

// Criar campos a partir do cabeçalho
$sortorder = 1;
foreach ($header as $column) {

    if (!str_contains($column, ':')) {
        cli_error("\ncoluna de cabeçalho inválida '$column'; deve estar no formato 'nome:tipo' (ex: 'name:text')");
    }

    list($name, $type) = explode(':', $column);

    $field = new stdClass();
    $field->dataid = $dataid;
    $field->name = trim($name);
    $field->description = '';
    $field->descriptionformat = FORMAT_HTML;
    $field->type = trim($type);
    $field->visible = 1;
    $field->required = 0;
    $field->sortorder = $sortorder++;

    // Parâmetros para tipos que precisam
    if ($field->type === 'text') {
        $field->param1 = 255; // tamanho máximo
    } elseif ($field->type === 'textarea') {
        $field->param1 = 30; // linhas
        $field->param2 = 50; // colunas
    } elseif ($field->type === 'number') {
        $field->param1 = 5; // casas decimais
    } elseif ($field->type === 'menu') {
        $field->param1 = "Opção 1\nOpção 2\nOpção 3"; // lista de opções separadas por \n
    } elseif ($field->type === 'radiobutton') {
        $field->param1 = "Sim\nNão\nTalvez"; // lista de opções separadas por \n
    }

    $DB->insert_record('data_fields', $field);

    echo "campo criado: {$name} ({$type})\n";
}

echo "\noperação concluída com sucesso.\n\n";
