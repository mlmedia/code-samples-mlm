<?php

namespace App\Http\Controllers;

use Ramsey\Uuid\Uuid;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

use stdClass;

class DataProcessController extends Controller
{
	/**
	* constructor
	*/
	public function __construct() {

	}

	/**
	* Index (list of links for other functions)
	*
	* @return View
	*/
	public function index()
	{
		$data = new stdClass;
		$data->link_base = '/admin/data-process-fg4390skamlfg33204';
		$data->list_links = array(
			'List Activities' => 'list-activities',
			'List Lessons' => 'list-lessons',
			'List Front Matters' => 'list-front-matters',
			'List Media' => 'list-media',
			'Temp Update Media' => 'temp-update-guids',
			'Temp Update Extensions' => 'temp-update-extensions'

		);
		$data->process_links = array(
			'Migrate Programs' => 'migrate-programs',
			'Migrate Classrooms' => 'migrate-classrooms',
			'Migrate Units' => 'migrate-units',
			'Migrate Unit Weeks' => 'migrate-unit-weeks',
			'Migrate Front Matter Types' => 'migrate-front-matter-types',
			'Migrate Front Matters' => 'migrate-front-matters',
			'Migrate Materials' => 'migrate-materials',

			//'Migrate Shopping Lists' => 'migrate-shopping-lists',
			//'Migrate Shopping List Materials' => 'migrate-shopping-list-materials',
			'Migrate Shopping List Categories' => 'migrate-shopping-list-categories',
			'Migrate Shopping Lists' => 'migrate-shopping-lists-new',
			'Migrate Shopping List Materials' => 'migrate-shopping-list-materials-new',
			'Migrate Activity Groups' => 'migrate-activity-groups',
			'Migrate Activity Types' => 'migrate-activity-types',
			'Migrate Activities' => 'migrate-activities',
			'Migrate Unit Week Activities' => 'migrate-unit-week-activities',
			'Migrate Activity Materials' => 'migrate-activity-materials',
			'Migrate Lesson Types' => 'migrate-lesson-types',
			'Migrate Lessons (LIMIT/OFFSET)' => 'migrate-lessons',
			'Migrate Lesson Assessments (LIMIT/OFFSET)' => 'migrate-lesson-assessments',
			'Migrate Lesson Instruction Types' => 'migrate-lesson-instruction-types',
			'Migrate Lesson Instructions (LIMIT/OFFSET)' => 'migrate-lesson-instructions',
			'Migrate Lesson Materials' => 'migrate-lesson-materials',
			'Migrate Learning Outcomes' => 'migrate-learning-outcomes',
			'Migrate Lesson Learning Outcomes (LIMIT/OFFSET)' => 'migrate-lesson-learning-outcomes',
			'Migrate Unit Week Lessons' => 'migrate-unit-week-lessons',
			'Migrate Media Types' => 'migrate-media-types',
			'Migrate Media' => 'migrate-media',
			'Migrate Lessons Media' => 'migrate-lesson-media',
			'Migrate Vocabulary' => 'migrate-vocabulary',
			'Migrate Lesson Vocabulary' => 'migrate-lesson-vocabulary'
		);
		return view('data-process-index', ['data' => $data]);
	}

	/**
	 * format the GUID to keep consistent across all tables
	 */
	public function format_guid($guid) {
		if (!$guid) {
			return '$guid is missing';
		}
		$output = strtoupper($guid);
		$output = str_replace('{', '', $output);
		$output = str_replace('}', '', $output);
		return $output;
	}

	/**
	* List Activities
	*
	* @return View
	*/
	public function list_activities()
	{
		$activities = DB::table('_import_programs')
			->join('_import_classrooms', '_import_classrooms.ProgramId', '=', '_import_programs.Id')
			->join('_import_units', '_import_units.ClassroomId', '=', '_import_classrooms.Id')
			->join('_import_unit_week', '_import_unit_week.UnitId', '=', '_import_units.Id')
			->join('_import_activities', '_import_activities.UnitWeekId', '=', '_import_unit_week.Id')
			->select(
				'_import_programs.Program',
				'_import_classrooms.Classroom',
				'_import_units.UnitNumberLabel',
				'_import_units.UnitTitle',
				'_import_unit_week.UnitWeekNumberLabel',
				'_import_activities.ActivityGroup',
				'_import_activities.ActivityType',
				'_import_activities.ActivityTitle',
				'_import_activities.Content'
			)
			->get();
		//return view('data-process-list-activities', ['data' => $data]);
		echo '<a href="/admin/data-process-fg4390skamlfg33204">Back to Data Processing Page</a>';
		echo '<pre>';
		print_r($activities);
		echo '</pre>';
	}

	/**
	* Temp Media GUID Update
	*
	* @return View
	*/
	public function temp_update_guids()
	{
		$items = DB::table('_import_media')->get();
		//return view('data-process-list-activities', ['data' => $data]);
		echo '<a href="/admin/data-process-fg4390skamlfg33204">Back to Data Processing Page</a>';
		$update = [];
		foreach ($items as $k => $v) {
			$update[$k]['Id'] = $v->Id;
			$url = $v->URL;
			$remove_path = 'https://documentmanagement.examplecompany.com/Document/resource/';
			$update[$k]['media_guid'] = str_replace($remove_path, '', $url);
			echo $update[$k]['Id'] . ': ' . $update[$k]['media_guid'] . '<br />';
		}
		DB::table('_import_media')->upsert(
			$update,
			['guid']
		);
	}

	/**
	* Temp Media Extension Update
	*
	* @return View
	*/
	public function temp_update_extensions()
	{
		$items = DB::table('_import_media')
			->join('_import_media_refs', '_import_media_refs.APIReferenceId', '=', '_import_media.media_guid')
			->select(
				'_import_media.Id as id',
				'_import_media_refs.FileName as filename'
			)
			->get();
		//return view('data-process-list-activities', ['data' => $data]);
		echo '<a href="/admin/data-process-fg4390skamlfg33204">Back to Data Processing Page</a>';

		$mime_types = [
			'mp3' => 'audio/mpeg',
			'mp4' => 'video/mp4',
			'wav' => 'audio/wav',
			'pdf' => 'application/pdf',
			'png' => 'image/png',
			'jpg' => 'image/jpeg',
			'jpeg' => 'image/jpeg',
			'svg' => 'image/svg+xml'
		];
		$update = [];
		foreach ($items as $k => $v) {
			$update[$k]['Id'] = $v->id;
			$info = pathinfo($v->filename);
			$extension = $info['extension'];
			$update[$k]['media_extension'] = $extension;
			$mime_type = $mime_types[$extension];
			$update[$k]['media_mime'] = $mime_type;
			echo $update[$k]['Id'] . ': ' . $update[$k]['media_extension'] . '<br />';
		}
		DB::table('_import_media')->upsert(
			$update,
			['guid']
		);
	}

	/* display media items (for debugging issues) */
	public function list_media() {
		$items = $this->get_media_items();
		echo '<pre>';
		print_r($items);
		echo '</pre>';
	}


	/**
	* Get Media Items for migration / display
	*/
	public function get_media_items()
	{
		$items = DB::table('_import_media')
			->join('_import_media_refs', '_import_media_refs.APIReferenceId', '=', '_import_media.media_guid')
			->select(
				'_import_media.Id as id',
				'_import_media.media_guid as media_guid',
				'_import_media_refs.DocumentId as doc_id',
				'_import_media_refs.APIReferenceId as api_id',
				'_import_media_refs.URL as url',
				'_import_media.media_mime as media_mime',
				'_import_media_refs.FileName as filename',
				'_import_media_refs.Description as desc',
				'_import_media_refs.HarmonyAndHeart as harmony',
				'_import_media_refs.SongTitle as song_title',
				'_import_media_refs.AlbumArtUrl as album_art_url',
				'_import_media_refs.AlbumArtFileName as album_art_filename',
			)
			->get();
		$output = [];
		foreach ($items as $k => $v) {
			$output[$k]['id'] = $this->format_guid($v->id);
			$output[$k]['media_guid'] = $this->format_guid($v->media_guid);
			$output[$k]['media_mime'] = $v->media_mime;
			$output[$k]['doc_id'] = $v->doc_id;
			$output[$k]['api_id'] = $this->format_guid($v->api_id);
			$output[$k]['file_name'] = $v->filename;
			$info = pathinfo($v->filename);
			$output[$k]['name'] = $info['filename'];
			$output[$k]['extension'] = $info['extension'];
		}
		return $output;
	}

	/**
	* List Lessons
	*/
	public function list_lessons()
	{
		$data = new stdClass;
		$data->lessons = DB::table('_import_programs')
			->join('_import_classrooms', '_import_classrooms.ProgramId', '=', '_import_programs.Id')
			->join('_import_units', '_import_units.ClassroomId', '=', '_import_classrooms.Id')
			->join('_import_unit_week', '_import_unit_week.UnitId', '=', '_import_units.Id')
			->join('_import_lessons', '_import_lessons.UnitWeekId', '=', '_import_unit_week.Id')
			->select(
				'_import_programs.Program',
				'_import_classrooms.Classroom',
				'_import_units.UnitNumberLabel',
				'_import_units.UnitTitle',
				'_import_unit_week.UnitWeekNumberLabel',
				'_import_lessons.LessonType',
				'_import_lessons.LessonTitle',
				'_import_lessons.LessonDay',
				//'_import_lessons.Content',
				'_import_lessons.Vocabulary',
			)
			->get();
		//return view('data-process-list-activities', ['data' => $data]);
		echo '<a href="/admin/data-process-fg4390skamlfg33204">Back to Data Processing Page</a>';
		echo '<pre>';
		print_r($data);
		echo '</pre>';
	}

	/**
	* List Front Matters
	*/
	public function list_front_matters()
	{
		$data = new stdClass;
		$data->front_matters = DB::table('_import_programs')
			->join('_import_classrooms', '_import_classrooms.ProgramId', '=', '_import_programs.Id')
			->join('_import_units', '_import_units.ClassroomId', '=', '_import_classrooms.Id')
			->join('_import_front_matters', '_import_front_matters.UnitId', '=', '_import_units.Id')
			->select(
				'_import_programs.Program as program',
				'_import_classrooms.Classroom as classroom',
				'_import_units.UnitNumberLabel as unit_no_label',
				'_import_units.UnitTitle as unit_title',
				'_import_front_matters.Type as front_matter_type',
				'_import_front_matters.Content as front_matter_content',
			)
			->get();
		//return view('data-process-list-activities', ['data' => $data]);
		echo '<a href="/admin/data-process-fg4390skamlfg33204">Back to Data Processing Page</a>';
		echo '<pre>';
		print_r($data);
		echo '</pre>';
	}


	/**
	* Activity Group array
	*/
	public function activity_group_array()
	{
		$items = DB::table('activity_groups')
			->select('name', 'id')
			->distinct()
			->get();
		$arr = [];
		foreach ($items as $k => $v) {
			$name = $v->name;
			$id = $v->id;
			if (isset($name) && isset($id)) {
				$arr[$name] = $id;
			}
		}
		return $arr;
	}

	/**
	* Activity Types array
	*/
	public function activity_type_array()
	{
		$items = DB::table('activity_types')
			->select('name', 'id')
			->distinct()
			->get();
		$arr = [];
		foreach ($items as $k => $v) {
			$name = $v->name;
			$id = $v->id;
			if (isset($name) && isset($id)) {
				$arr[$name] = $id;
			}
		}
		return $arr;
	}

	/**
	* Programs array
	*/
	public function program_array()
	{
		$items = DB::table('programs')
			->select('guid', 'id')
			->distinct()
			->get();
		$arr = [];
		foreach ($items as $k => $v) {
			$guid = $v->guid;
			$id = $v->id;
			if (isset($guid) && isset($id)) {
				$arr[$guid] = $id;
			}
		}
		return $arr;
	}

	/**
	* Classrooms array
	*/
	public function classroom_array()
	{
		$items = DB::table('classrooms')
			->select('guid', 'id')
			->distinct()
			->get();
		$arr = [];
		foreach ($items as $k => $v) {
			$guid = $v->guid;
			$id = $v->id;
			if (isset($guid) && isset($id)) {
				$arr[$guid] = $id;
			}
		}
		return $arr;
	}

	/**
	* Units array
	*/
	public function unit_array()
	{
		$items = DB::table('units')
			->select('guid', 'id')
			->distinct()
			->get();
		$arr = [];
		foreach ($items as $k => $v) {
			$guid = $v->guid;
			$id = $v->id;
			if (isset($guid) && isset($id)) {
				$arr[$guid] = $id;
			}
		}
		return $arr;
	}

	/**
	* Unit Weeks array
	*/
	public function unit_week_array()
	{
		$items = DB::table('unit_weeks')
			->select('guid', 'id')
			->distinct()
			->get();
		$arr = [];
		foreach ($items as $k => $v) {
			$guid = $v->guid;
			$id = $v->id;
			if (isset($guid) && isset($id)) {
				$arr[$guid] = $id;
			}
		}
		return $arr;
	}

	/**
	* Category array (shopping list categories)
	*/
	public function category_array()
	{
		$items = DB::table('categories')
			->select('name', 'id')
			->distinct()
			->get();
		$arr = [];
		foreach ($items as $k => $v) {
			$name = $v->name;
			$id = $v->id;
			if (isset($name) && isset($id)) {
				$arr[$name] = $id;
			}
		}
		return $arr;
	}

	/**
	* Activities array
	*/
	public function activity_array()
	{
		$items = DB::table('activities')
			->select('guid', 'id')
			->distinct()
			->get();
		$arr = [];
		foreach ($items as $k => $v) {
			$guid = $v->guid;
			$id = $v->id;
			if (isset($guid) && isset($id)) {
				$arr[$guid] = $id;
			}
		}
		return $arr;
	}

	/**
	* Materials array
	*/
	public function material_array()
	{
		$items = DB::table('materials')
			->select('guid', 'id')
			->distinct()
			->get();
		$arr = [];
		foreach ($items as $k => $v) {
			$guid = $v->guid;
			$id = $v->id;
			if (isset($guid) && isset($id)) {
				$arr[$guid] = $id;
			}
		}
		return $arr;
	}

	/**
	* Shopping List array
	*/
	public function shopping_list_array()
	{
		$items = DB::table('unit_shopping_lists')
			->select('guid', 'id')
			->distinct()
			->get();
		$arr = [];
		foreach ($items as $k => $v) {
			$guid = $v->guid;
			$id = $v->id;
			if (isset($guid) && isset($id)) {
				$arr[$guid] = $id;
			}
		}
		return $arr;
	}

	/**
	* Shopping List array
	*/
	public function shopping_list_array_new()
	{
		$items = DB::table('unit_week_shopping_lists')
			->select('guid', 'id')
			->distinct()
			->get();
		$arr = [];
		foreach ($items as $k => $v) {
			$guid = $v->guid;
			$id = $v->id;
			if (isset($guid) && isset($id)) {
				$arr[$guid] = $id;
			}
		}
		return $arr;
	}

	/**
	* Front Matter Type array
	*/
	public function front_matter_type_array()
	{
		$items = DB::table('front_matter_types')
			->select('name', 'id')
			->distinct()
			->get();
		$arr = [];
		foreach ($items as $k => $v) {
			$name = $v->name;
			$id = $v->id;
			if (isset($name) && isset($id)) {
				$arr[$name] = $id;
			}
		}
		return $arr;
	}

	/**
	* Lesson Types array
	*/
	public function lesson_type_array()
	{
		$items = DB::table('lesson_types')
			->select('lesson_type', 'id')
			->distinct()
			->get();
		$arr = [];
		foreach ($items as $k => $v) {
			$lesson_type = $v->lesson_type;
			$id = $v->id;
			if (isset($lesson_type) && isset($id)) {
				$arr[$lesson_type] = $id;
			}
		}
		return $arr;
	}

	/**
	* Lesson Instruction Types array
	*/
	public function lesson_instruction_type_array()
	{
		$items = DB::table('lesson_instruction_types')
			->select('type', 'id')
			->distinct()
			->get();
		$arr = [];
		foreach ($items as $k => $v) {
			$type = $v->type;
			$id = $v->id;
			if (isset($type) && isset($id)) {
				$arr[$type] = $id;
			}
		}
		return $arr;
	}

	/**
	* Lesson array
	*/
	public function lesson_array()
	{
		$items = DB::table('lessons')
			->select('guid', 'id')
			->distinct()
			->get();
		$arr = [];
		foreach ($items as $k => $v) {
			$guid = $this->format_guid($v->guid);
			$id = $v->id;
			if (isset($guid) && isset($id)) {
				$arr[$guid] = $id;
			}
		}
		return $arr;
	}

	/**
	* Learning Outcomes array
	*/
	public function learning_outcome_array()
	{
		$items = DB::table('learning_outcomes')
			->select('guid', 'id')
			->distinct()
			->get();
		$arr = [];
		foreach ($items as $k => $v) {
			$guid = $this->format_guid($v->guid);
			$id = $v->id;
			if (isset($guid) && isset($id)) {
				$arr[$guid] = $id;
			}
		}
		return $arr;
	}

	/**
	* Media Types array
	*/
	public function media_type_array()
	{
		$items = DB::table('media_types')
			->select('type', 'id')
			->distinct()
			->get();
		$arr = [];
		foreach ($items as $k => $v) {
			$type = $v->type;
			$id = $v->id;
			if (isset($type) && isset($id)) {
				$arr[$type] = $id;
			}
		}
		return $arr;
	}

	/**
	* Media array
	*/
	public function media_array()
	{
		$items = DB::table('media_library')
			->select('guid', 'id')
			->distinct()
			->get();
		$arr = [];
		foreach ($items as $k => $v) {
			$guid = $v->guid;
			$id = $v->id;
			if (isset($guid) && isset($id)) {
				$arr[$guid] = $id;
			}
		}
		return $arr;
	}

	/**
	* Vocabulary array
	*/
	public function vocabulary_array()
	{
		$items = DB::table('vocabularies')
			->select('word', 'id')
			->distinct()
			->get();
		$arr = [];
		foreach ($items as $k => $v) {
			$word = $v->word;
			$id = $v->id;
			if (isset($word) && isset($id)) {
				$arr[$word] = $id;
			}
		}
		return $arr;
	}

	/**
	* Migrate Programs
	*/
	public function migrate_programs()
	{
		$programs = DB::table('_import_programs')
			->get();
		//return view('data-process-list-activities', ['data' => $data]);
		echo '<div class="mb-8">';
		echo '<a href="/admin/data-process-fg4390skamlfg33204">Back to Data Processing Page</a>';
		echo '</div>';
		$insert = [];
		foreach ($programs as $k => $v) {
			$insert[$k]['guid'] = $this->format_guid($v->Id);
			$insert[$k]['name'] = $v->Program;
			$insert[$k]['is_active'] = 1;
			$insert[$k]['created_at'] = date('Y-m-d H:i:s');
			$insert[$k]['updated_at'] = date('Y-m-d H:i:s');
			echo $insert[$k]['guid'] . ': ' . $insert[$k]['name'] . '<br />';
		}
		// echo '<pre>';
		// print_r($insert);
		// echo '</pre>';
		DB::table('programs')->upsert(
			$insert,
			['guid']
		);
	}

	/**
	* Migrate Classrooms
	*/
	public function migrate_classrooms()
	{
		$classrooms = DB::table('_import_classrooms')
			->get();
		$programs = $this->program_array();
		//return view('data-process-list-activities', ['data' => $data]);
		echo '<div class="mb-8">';
		echo '<a href="/admin/data-process-fg4390skamlfg33204">Back to Data Processing Page</a>';
		echo '</div>';
		$insert = [];
		foreach ($classrooms as $k => $v) {
			$insert[$k]['guid'] = $this->format_guid($v->Id);
			$p_guid = $this->format_guid($v->ProgramId);
			$p_id = $programs[$p_guid];
			//$insert[$k]['program_id'] = $this->format_guid($v->ProgramId);
			$insert[$k]['name'] = $v->Classroom;
			$insert[$k]['program_id'] = $p_id;
			$insert[$k]['is_active'] = 1;
			$insert[$k]['created_at'] = date('Y-m-d H:i:s');
			$insert[$k]['updated_at'] = date('Y-m-d H:i:s');
			echo $insert[$k]['guid'] . ': ' . $insert[$k]['name'] . '<br />';
		}
		// echo '<pre>';
		// print_r($insert);
		// echo '</pre>';
		DB::table('classrooms')->upsert(
			$insert,
			['guid']
		);
	}

	/**
	* Migrate Units
	*/
	public function migrate_units()
	{
		$units = DB::table('_import_units')
			->get();
		$classrooms = $this->classroom_array();
		//return view('data-process-list-activities', ['data' => $data]);
		echo '<div class="mb-8">';
		echo '<a href="/admin/data-process-fg4390skamlfg33204">Back to Data Processing Page</a>';
		echo '</div>';
		$insert = [];
		foreach ($units as $k => $v) {
			$insert[$k]['guid'] = $this->format_guid($v->Id);
			$c_guid = $this->format_guid($v->ClassroomId);
			$c_id = $classrooms[$c_guid];
			$unit_no = str_replace('Unit', '', $v->UnitNumberLabel);
			if (strtolower($unit_no) == 'welcome to school') {
				$unit_no = 0;
			}
			//$insert[$k]['program_id'] = $this->format_guid($v->ProgramId);
			$insert[$k]['unit_title'] = $v->UnitTitle;
			$insert[$k]['classroom_id'] = $c_id;
			$insert[$k]['unit_number'] = $unit_no;
			$insert[$k]['unit_number_label'] = $v->UnitNumberLabel;
			$insert[$k]['is_active'] = 1;
			$insert[$k]['created_at'] = date('Y-m-d H:i:s');
			$insert[$k]['updated_at'] = date('Y-m-d H:i:s');
			echo $insert[$k]['guid'] . ': ' . $insert[$k]['unit_number_label'] . '--' . $insert[$k]['unit_title'] . '<br />';
		}
		// echo '<pre>';
		// print_r($insert);
		// echo '</pre>';
		DB::table('units')->upsert(
			$insert,
			['guid']
		);
	}

	/**
	* Migrate Front Matter Types
	*/
	public function migrate_front_matter_types()
	{
		$front_matter_types = DB::table('_import_front_matters')
			->select('Type')
			->distinct()
			->get();
		echo '<div class="mb-8">';
		echo '<a href="/admin/data-process-fg4390skamlfg33204">Back to Data Processing Page</a>';
		echo '</div>';
		$insert = [];
		foreach ($front_matter_types as $k => $v) {
			$uuid = Uuid::uuid1()->toString();
			$insert[$k]['guid'] = $this->format_guid($uuid);
			$insert[$k]['name'] = $v->Type;
			$insert[$k]['is_active'] = 1;
			$insert[$k]['created_at'] = date('Y-m-d H:i:s');
			$insert[$k]['updated_at'] = date('Y-m-d H:i:s');
			echo $insert[$k]['guid'] . ': ' . $insert[$k]['name'] . '<br />';
		}
		// DB::table('front_matter_types')->upsert(
		// 	$insert,
		// 	['name', 'guid']
		// );
	}

	/**
	* Migrate Front Matters
	*/
	public function migrate_front_matters()
	{
		$front_matters = DB::table('_import_front_matters')
			->get();
		$units = $this->unit_array();
		$front_matter_types = $this->front_matter_type_array();
		//return view('data-process-list-activities', ['data' => $data]);
		echo '<div class="mb-8">';
		echo '<a href="/admin/data-process-fg4390skamlfg33204">Back to Data Processing Page</a>';
		echo '</div>';
		$insert = [];
		foreach ($front_matters as $k => $v) {
			$insert[$k]['guid'] = $this->format_guid($v->Id);
			$u_guid = $this->format_guid($v->UnitId);
			$u_id = $units[$u_guid];
			$ft_id = $front_matter_types[$v->Type];
			$insert[$k]['type'] = $v->Type;
			$insert[$k]['unit_id'] = $u_id;
			$insert[$k]['front_matter_type_id'] = $ft_id;
			$insert[$k]['content'] = $v->Content;
			$insert[$k]['is_active'] = 1;
			$insert[$k]['created_at'] = date('Y-m-d H:i:s');
			$insert[$k]['updated_at'] = date('Y-m-d H:i:s');
			echo $insert[$k]['guid'] . ': ' . $insert[$k]['type'] . '<br />';
		}
		// echo '<pre>';
		// print_r($insert);
		// echo '</pre>';
		DB::table('front_matters')->upsert(
			$insert,
			['guid']
		);
	}

	/**
	* Migrate Materials
	*/
	public function migrate_materials()
	{
		$materials = DB::table('_import_materials')
			->get();
		//return view('data-process-list-activities', ['data' => $data]);
		echo '<div class="mb-8">';
		echo '<a href="/admin/data-process-fg4390skamlfg33204">Back to Data Processing Page</a>';
		echo '</div>';
		$insert = [];
		foreach ($materials as $k => $v) {
			$insert[$k]['guid'] = $this->format_guid($v->Id);
			$insert[$k]['name'] = $v->Material;
			$insert[$k]['is_active'] = 1;
			$insert[$k]['created_at'] = date('Y-m-d H:i:s');
			$insert[$k]['updated_at'] = date('Y-m-d H:i:s');
			echo $insert[$k]['guid'] . ': ' . $insert[$k]['name'] . '<br />';
		}
		// echo '<pre>';
		// print_r($insert);
		// echo '</pre>';
		DB::table('materials')->upsert(
			$insert,
			['guid']
		);
	}

	/**
	* Migrate Unit Weeks
	*/
	public function migrate_unit_weeks()
	{
		$items = DB::table('_import_unit_week')
			->get();
		$units = $this->unit_array();
		//return view('data-process-list-activities', ['data' => $data]);
		echo '<div class="mb-8">';
		echo '<a href="/admin/data-process-fg4390skamlfg33204">Back to Data Processing Page</a>';
		echo '</div>';
		$insert = [];
		foreach ($items as $k => $v) {
			$insert[$k]['guid'] = $this->format_guid($v->Id);
			$u_guid = $this->format_guid($v->UnitId);
			$u_id = $units[$u_guid];
			$insert[$k]['unit_intro'] = $v->UnitIntro;
			$insert[$k]['unit_week_number_label'] = $v->UnitWeekNumberLabel;
			$week_number = str_replace('Week ', '', $v->UnitWeekNumberLabel);
			$insert[$k]['week_number'] = strlen($week_number) > 0 ? $week_number : 0;
			$insert[$k]['unit_id'] = $u_id;
			$insert[$k]['is_active'] = 1;
			$insert[$k]['created_at'] = date('Y-m-d H:i:s');
			$insert[$k]['updated_at'] = date('Y-m-d H:i:s');
			echo $insert[$k]['guid'] . ': ' . $insert[$k]['unit_week_number_label'] . '<br />';
		}
		// echo '<pre>';
		// print_r($insert);
		// echo '</pre>';
		DB::table('unit_weeks')->upsert(
			$insert,
			['guid']
		);
	}

	/**
	* Migrate Shopping List Categories
	*/
	public function migrate_shopping_list_categories()
	{
		$items = DB::table('_import_unit_shopping_lists')
			->select('ListLesson')
			->distinct()
			->get();
		echo '<div class="mb-8">';
		echo '<a href="/admin/data-process-fg4390skamlfg33204">Back to Data Processing Page</a>';
		echo '</div>';

		$insert = [];
		foreach ($items as $k => $v) {
			if (
				isset($v->ListLesson) &&
				strlen($v->ListLesson) > 0
			) {
				$insert[$k]['name'] = $v->ListLesson;
				$insert[$k]['is_active'] = 1;
				$insert[$k]['created_at'] = date('Y-m-d H:i:s');
				$insert[$k]['updated_at'] = date('Y-m-d H:i:s');
				echo $insert[$k]['name'] . '<br />';
			}
		}
		DB::table('categories')->upsert(
			$insert,
			['name']
		);
	}

	/**
	* Migrate Shopping Lists
	*/
	public function migrate_shopping_lists()
	{
		$shopping_lists = DB::table('_import_unit_shopping_lists')
			->get();
		$units = $this->unit_array();
		//return view('data-process-list-activities', ['data' => $data]);
		echo '<div class="mb-8">';
		echo '<a href="/admin/data-process-fg4390skamlfg33204">Back to Data Processing Page</a>';
		echo '</div>';
		$insert = [];
		foreach ($shopping_lists as $k => $v) {
			$insert[$k]['guid'] = $this->format_guid($v->Id);
			$u_guid = $this->format_guid($v->UnitId);
			$u_id = $units[$u_guid];
			$insert[$k]['list_week'] = $v->ListWeek ? $v->ListWeek : 0;
			$insert[$k]['list_lesson'] = $v->ListLesson;
			$insert[$k]['unit_id'] = $u_id;
			$insert[$k]['is_active'] = 1;
			$insert[$k]['created_at'] = date('Y-m-d H:i:s');
			$insert[$k]['updated_at'] = date('Y-m-d H:i:s');
			echo $insert[$k]['guid'] . ': ' . $insert[$k]['list_lesson'] . '<br />';
		}
		echo '<pre>';
		print_r($insert);
		echo '</pre>';
		DB::table('unit_week_shopping_lists')->upsert(
			$insert,
			['guid']
		);
	}

	/**
	* Migrate Shopping List Materials
	*/
	public function migrate_shopping_list_materials()
	{
		$items = DB::table('_import_shopping_list_materials')
			->get();
		$materials = $this->material_array();
		$shopping_lists = $this->shopping_list_array();
		//return view('data-process-list-activities', ['data' => $data]);
		echo '<div class="mb-8">';
		echo '<a href="/admin/data-process-fg4390skamlfg33204">Back to Data Processing Page</a>';
		echo '</div>';
		$insert = [];
		foreach ($items as $k => $v) {
			$m_guid = $this->format_guid($v->MaterialId);
			$m_id = $materials[$m_guid];
			$sl_guid = $this->format_guid($v->UnitShoppingListId);
			$sl_id = $shopping_lists[$sl_guid];
			$insert[$k]['material_id'] = $m_id;
			$insert[$k]['unit_shopping_list_id'] = $sl_id;
			$insert[$k]['is_active'] = 1;
			$insert[$k]['created_at'] = date('Y-m-d H:i:s');
			$insert[$k]['updated_at'] = date('Y-m-d H:i:s');
			echo $insert[$k]['material_id'] . ': ' . $insert[$k]['unit_shopping_list_id'] . '<br />';
		}
		// echo '<pre>';
		// print_r($insert);
		// echo '</pre>';
		DB::table('shopping_list_materials')->upsert(
			$insert,
			['material_id', 'unit_shopping_list_id']
		);
	}

	/**
	* Migrate Activity Groups
	*/
	public function migrate_activity_groups()
	{
		$activity_groups = DB::table('_import_activities')
			->select('ActivityGroup')
			->distinct()
			->get();
		echo '<div class="mb-8">';
		echo '<a href="/admin/data-process-fg4390skamlfg33204">Back to Data Processing Page</a>';
		echo '</div>';
		$insert = [];
		foreach ($activity_groups as $k => $v) {
			$uuid = Uuid::uuid1()->toString();
			$insert[$k]['guid'] = $this->format_guid($uuid);
			$insert[$k]['name'] = $v->ActivityGroup;
			$insert[$k]['is_active'] = 1;
			$insert[$k]['created_at'] = date('Y-m-d H:i:s');
			$insert[$k]['updated_at'] = date('Y-m-d H:i:s');
			echo $insert[$k]['guid'] . ': ' . $insert[$k]['name'] . '<br />';
		}
		// DB::table('activity_groups')->upsert(
		// 	$insert,
		// 	['name', 'guid']
		// );
	}

	/**
	* Migrate Activity Types
	*/
	public function migrate_activity_types()
	{
		$activity_types = DB::table('_import_activities')
			->select('ActivityType', 'ActivityGroup')
			->distinct()
			->get();
		echo '<div class="mb-8">';
		echo '<a href="/admin/data-process-fg4390skamlfg33204">Back to Data Processing Page</a>';
		echo '</div>';
		$activity_groups = $this->activity_group_array();
		$insert = [];
		foreach ($activity_types as $k => $v) {
			$uuid = Uuid::uuid1()->toString();
			$ag_id = $activity_groups[$v->ActivityGroup];
			$insert[$k]['guid'] = $this->format_guid($uuid);
			$insert[$k]['name'] = $v->ActivityType;
			$insert[$k]['activity_group_id'] = $ag_id;
			$insert[$k]['is_active'] = 1;
			$insert[$k]['created_at'] = date('Y-m-d H:i:s');
			$insert[$k]['updated_at'] = date('Y-m-d H:i:s');
			echo $insert[$k]['guid'] . ': ' . $insert[$k]['name'] . '<br />';
		}
		// DB::table('activity_types')->upsert(
		// 	$insert,
		// 	['name', 'guid']
		// );
	}

	/**
	* Migrate Activities
	*/
	public function migrate_activities()
	{
		$activities = DB::table('_import_activities')
			->get();
		$activity_types = DB::table('_import_activities')
			->select('ActivityGroup')
			->distinct()
			->get();
		$activity_types = $this->activity_type_array();
		//return view('data-process-list-activities', ['data' => $data]);
		echo '<div class="mb-8">';
		echo '<a href="/admin/data-process-fg4390skamlfg33204">Back to Data Processing Page</a>';
		echo '</div>';
		$insert = [];
		foreach ($activities as $k => $v) {
			$at_id = $activity_types[$v->ActivityType];
			$insert[$k]['guid'] = $this->format_guid($v->Id);
			$insert[$k]['activity_title'] = $v->ActivityTitle;
			$insert[$k]['activity_type_id'] = $at_id;
			$insert[$k]['activity_group'] = $v->ActivityGroup;
			$insert[$k]['activity_type'] = $v->ActivityType;
			$insert[$k]['activity_day'] = $v->ActivityDay;
			$insert[$k]['content'] = $v->Content;
			$insert[$k]['created_at'] = date('Y-m-d H:i:s');
			$insert[$k]['updated_at'] = date('Y-m-d H:i:s');
			echo $insert[$k]['guid'] . ': ' . $insert[$k]['activity_title'] . '<br />';
		}
		// echo '<pre>';
		// print_r($insert);
		// echo '</pre>';
		DB::table('activities')->upsert(
			$insert,
			['guid']
		);
	}

	/**
	* Migrate Unit Week Activities
	*/
	public function migrate_unit_week_activities()
	{
		$items = DB::table('_import_weekly_activities')
			->get();
		$unit_weeks = $this->unit_week_array();
		$activities = $this->activity_array();

		//return view('data-process-list-activities', ['data' => $data]);
		echo '<div class="mb-8">';
		echo '<a href="/admin/data-process-fg4390skamlfg33204">Back to Data Processing Page</a>';
		echo '</div>';
		$insert = [];
		foreach ($items as $k => $v) {
			$uw_guid = $this->format_guid($v->UnitWeekId);
			$uw_id = $unit_weeks[$uw_guid];
			$a_guid = $this->format_guid($v->ActivityId);
			$a_id = $activities[$a_guid];
			$insert[$k]['unit_week_id'] = $uw_id;
			$insert[$k]['activity_id'] = $a_id;
			$insert[$k]['is_active'] = 1;
			$insert[$k]['created_at'] = date('Y-m-d H:i:s');
			$insert[$k]['updated_at'] = date('Y-m-d H:i:s');
			echo $insert[$k]['unit_week_id'] . ': ' . $insert[$k]['activity_id'] . '<br />';
		}
		// echo '<pre>';
		// print_r($insert);
		// echo '</pre>';
		DB::table('unit_week_activities')->upsert(
			$insert,
			['unit_week_id', 'activity_id']
		);
	}

	/**
	* Migrate Unit Week Activities
	*/
	public function migrate_activity_materials()
	{
		$items = DB::table('_import_activity_materials')
			->get();
		$materials = $this->material_array();
		$activities = $this->activity_array();

		//return view('data-process-list-activities', ['data' => $data]);
		echo '<div class="mb-8">';
		echo '<a href="/admin/data-process-fg4390skamlfg33204">Back to Data Processing Page</a>';
		echo '</div>';
		$insert = [];
		foreach ($items as $k => $v) {
			$m_guid = $this->format_guid($v->MaterialId);
			$m_id = $materials[$m_guid];
			$a_guid = $this->format_guid($v->ActivityId);
			$a_id = $activities[$a_guid];
			$insert[$k]['material_id'] = $m_id;
			$insert[$k]['activity_id'] = $a_id;
			$insert[$k]['is_active'] = 1;
			$insert[$k]['created_at'] = date('Y-m-d H:i:s');
			$insert[$k]['updated_at'] = date('Y-m-d H:i:s');
			echo $insert[$k]['material_id'] . ': ' . $insert[$k]['activity_id'] . '<br />';
		}
		// echo '<pre>';
		// print_r($insert);
		// echo '</pre>';
		DB::table('activity_materials')->upsert(
			$insert,
			['material_id', 'activity_id']
		);
	}

	/**
	* Migrate Lesson Types
	*/
	public function migrate_lesson_types()
	{
		$types = DB::table('_import_lessons')
			->select('LessonType')
			->distinct()
			->get();
		echo '<div class="mb-8">';
		echo '<a href="/admin/data-process-fg4390skamlfg33204">Back to Data Processing Page</a>';
		echo '</div>';
		$insert = [];
		foreach ($types as $k => $v) {
			$uuid = Uuid::uuid1()->toString();
			$insert[$k]['guid'] = $this->format_guid($uuid);
			$insert[$k]['lesson_type'] = $v->LessonType;
			$insert[$k]['is_active'] = 1;
			$insert[$k]['created_at'] = date('Y-m-d H:i:s');
			$insert[$k]['updated_at'] = date('Y-m-d H:i:s');
			echo $insert[$k]['guid'] . ': ' . $insert[$k]['lesson_type'] . '<br />';
		}
		// DB::table('lesson_types')->upsert(
		// 	$insert,
		// 	['name', 'guid']
		// );
	}

	/**
	* Migrate Lessons
	*/
	public function migrate_lessons()
	{
		/* using offset for larger tables to process only ~500 per run */
		/* NOTE: just change the offset number by 500 each run */
		$items = DB::table('_import_lessons')
			->offset(0)
			->limit(500)
			->get();
		$lesson_types = $this->lesson_type_array();

		//return view('data-process-list-activities', ['data' => $data]);
		echo '<div class="mb-8">';
		echo '<a href="/admin/data-process-fg4390skamlfg33204">Back to Data Processing Page</a>';
		echo '</div>';
		$insert = [];
		foreach ($items as $k => $v) {
			$insert[$k]['guid'] = $this->format_guid($v->Id);
			$lt_guid = trim($v->LessonType);
			$lt_id = $lesson_types[$lt_guid];
			//$insert[$k]['unit_week_id'] = $uw_id;
			$lesson_day = str_replace('Day', '', $v->LessonDay);
			$lesson_day = str_replace('Week', '', $lesson_day);
			$lesson_day = trim($lesson_day);
			$lesson_day = strlen($lesson_day) > 0 ? $lesson_day : 0;
			$insert[$k]['lesson_type_id'] = $lt_id;
			$insert[$k]['lesson_type'] = $v->LessonType;
			$insert[$k]['lesson_title'] = $v->LessonTitle;
			$insert[$k]['lesson_day'] = $lesson_day;
			$insert[$k]['content'] = $v->Content;
			//$insert[$k]['vocabulary'] = $v->Vocabulary;
			$insert[$k]['is_active'] = 1;
			$insert[$k]['created_at'] = date('Y-m-d H:i:s');
			$insert[$k]['updated_at'] = date('Y-m-d H:i:s');
			echo $insert[$k]['lesson_type'] . ': ' . $insert[$k]['lesson_title'] . '<br />';
		}
		// echo '<pre>';
		// print_r($insert);
		// echo '</pre>';
		DB::table('lessons')->upsert(
			$insert,
			['guid']
		);
	}

	/**
	* Migrate Lessons
	*/
	public function migrate_lesson_assessments()
	{
		/* using offset for larger tables to process only ~500 per run */
		/* NOTE: just change the offset number by 500 each run */
		$items = DB::table('_import_lesson_assessments')
			->offset(0)
			->limit(300)
			->get();
		$lessons = $this->lesson_array();

		//return view('data-process-list-activities', ['data' => $data]);
		echo '<div class="mb-8">';
		echo '<a href="/admin/data-process-fg4390skamlfg33204">Back to Data Processing Page</a>';
		echo '</div>';
		$insert = [];
		foreach ($items as $k => $v) {
			$insert[$k]['guid'] = $this->format_guid($v->Id);
			$l_guid = $this->format_guid($v->LessonId);
			$l_id = $lessons[$l_guid];
			//$insert[$k]['unit_week_id'] = $uw_id;
			$insert[$k]['lesson_id'] = $l_id;
			$insert[$k]['assessment'] = $v->Assesment;
			$insert[$k]['is_active'] = 1;
			$insert[$k]['created_at'] = date('Y-m-d H:i:s');
			$insert[$k]['updated_at'] = date('Y-m-d H:i:s');
			echo $insert[$k]['lesson_id'] . ': ' . $insert[$k]['assessment'] . '<br />';
		}
		// echo '<pre>';
		// print_r($insert);
		// echo '</pre>';
		DB::table('lesson_assessments')->upsert(
			$insert,
			['guid']
		);
	}

	/**
	* Migrate Lesson Instruction Types
	*/
	public function migrate_lesson_instruction_types()
	{
		$types = DB::table('_import_lesson_instructions')
			->select('Type')
			->distinct()
			->get();
		echo '<div class="mb-8">';
		echo '<a href="/admin/data-process-fg4390skamlfg33204">Back to Data Processing Page</a>';
		echo '</div>';
		$insert = [];
		foreach ($types as $k => $v) {
			$uuid = Uuid::uuid1()->toString();
			$insert[$k]['guid'] = $this->format_guid($uuid);
			$insert[$k]['type'] = $v->Type;
			$insert[$k]['created_at'] = date('Y-m-d H:i:s');
			$insert[$k]['updated_at'] = date('Y-m-d H:i:s');
			echo $insert[$k]['guid'] . ': ' . $insert[$k]['type'] . '<br />';
		}
		// DB::table('lesson_instruction_types')->upsert(
		// 	$insert,
		// 	['type']
		// );
	}

	/**
	* Migrate Lessons
	*/
	public function migrate_lesson_instructions()
	{
		/* using offset for larger tables to process only ~500 per run */
		/* NOTE: just change the offset number by 500 each run */
		$items = DB::table('_import_lesson_instructions')
			->offset(0)
			->limit(500)
			->get();
		$lessons = $this->lesson_array();
		$lesson_instruction_types = $this->lesson_instruction_type_array();

		//return view('data-process-list-activities', ['data' => $data]);
		echo '<div class="mb-8">';
		echo '<a href="/admin/data-process-fg4390skamlfg33204">Back to Data Processing Page</a>';
		echo '</div>';
		$insert = [];
		foreach ($items as $k => $v) {
			$insert[$k]['guid'] = $this->format_guid($v->Id);
			$l_guid = $this->format_guid($v->LessonId);
			$l_id = isset($lessons[$l_guid]) ? $lessons[$l_guid] : null;
			$lit_guid = $v->Type;
			$lit_id = $lesson_instruction_types[$lit_guid];
			//$insert[$k]['unit_week_id'] = $uw_id;
			$insert[$k]['lesson_id'] = $l_id;
			$insert[$k]['lesson_instruction_type_id'] = $lit_id;
			$insert[$k]['content'] = $v->Content;
			$insert[$k]['is_active'] = 1;
			$insert[$k]['created_at'] = date('Y-m-d H:i:s');
			$insert[$k]['updated_at'] = date('Y-m-d H:i:s');
			echo $insert[$k]['lesson_id'] . ': ' . $insert[$k]['lesson_instruction_type_id'] . '<br />';
		}
		// echo '<pre>';
		// print_r($insert);
		// echo '</pre>';
		DB::table('lesson_instructions')->upsert(
			$insert,
			['guid']
		);
	}

	/**
	* Migrate Lesson Materials
	*/
	public function migrate_lesson_materials()
	{
		$items = DB::table('_import_lesson_materials')
			->get();
		$materials = $this->material_array();
		$lessons = $this->lesson_array();
		//return view('data-process-list-activities', ['data' => $data]);
		echo '<div class="mb-8">';
		echo '<a href="/admin/data-process-fg4390skamlfg33204">Back to Data Processing Page</a>';
		echo '</div>';
		$insert = [];
		foreach ($items as $k => $v) {
			$m_guid = $this->format_guid($v->MaterialId);
			$m_id = isset($materials[$m_guid]) ? $materials[$m_guid] : null;
			$l_guid = $this->format_guid($v->LessonId);
			$l_id = isset($lessons[$l_guid]) ? $lessons[$l_guid] : null;
			if ($l_id && $m_id) {
				$insert[$k]['material_id'] = $m_id;
				$insert[$k]['lesson_id'] = $l_id;
				$insert[$k]['is_active'] = 1;
				$insert[$k]['created_at'] = date('Y-m-d H:i:s');
				$insert[$k]['updated_at'] = date('Y-m-d H:i:s');
				echo $insert[$k]['material_id'] . ': ' . $insert[$k]['lesson_id'] . '<br />';
			}
		}
		// echo '<pre>';
		// print_r($insert);
		// echo '</pre>';
		DB::table('lesson_materials')->upsert(
			$insert,
			['lesson_id', 'material_id']
		);
	}

	/**
	* Migrate Learning Outcomes
	*/
	public function migrate_learning_outcomes()
	{
		$items = DB::table('_import_learning_outcomes')
			->get();
		//return view('data-process-list-activities', ['data' => $data]);
		echo '<div class="mb-8">';
		echo '<a href="/admin/data-process-fg4390skamlfg33204">Back to Data Processing Page</a>';
		echo '</div>';
		$insert = [];
		foreach ($items as $k => $v) {
			$insert[$k]['guid'] = $this->format_guid($v->Id);
			$insert[$k]['code'] = $v->Code;
			$insert[$k]['description'] = $v->Description;
			$insert[$k]['is_active'] = 1;
			$insert[$k]['created_at'] = date('Y-m-d H:i:s');
			$insert[$k]['updated_at'] = date('Y-m-d H:i:s');
			echo $insert[$k]['guid'] . ': ' . $insert[$k]['code'] . '<br />';
		}
		// echo '<pre>';
		// print_r($insert);
		// echo '</pre>';
		DB::table('learning_outcomes')->upsert(
			$insert,
			['guid']
		);
	}

	/**
	* Migrate Unit Week Activities
	*/
	public function migrate_lesson_learning_outcomes()
	{
		$items = DB::table('_import_lesson_learning_outcomes')
			->offset(10000)
			->limit(5000)
			->get();
		$lessons = $this->lesson_array();
		$learning_outcomes = $this->learning_outcome_array();

		//return view('data-process-list-activities', ['data' => $data]);
		echo '<div class="mb-8">';
		echo '<a href="/admin/data-process-fg4390skamlfg33204">Back to Data Processing Page</a>';
		echo '</div>';
		$insert = [];
		foreach ($items as $k => $v) {
			$l_guid = $this->format_guid($v->LessonId);
			$l_id = isset($lessons[$l_guid]) ? $lessons[$l_guid] : null;
			$lo_guid = $this->format_guid($v->LearningOutcomeId);
			$lo_id = isset($learning_outcomes[$lo_guid]) ? $learning_outcomes[$lo_guid] : null;
			if ($l_id && $lo_id) {
				$insert[$k]['lesson_id'] = $l_id;
				$insert[$k]['learning_outcome_id'] = $lo_id;
				$insert[$k]['is_active'] = 1;
				$insert[$k]['created_at'] = date('Y-m-d H:i:s');
				$insert[$k]['updated_at'] = date('Y-m-d H:i:s');
				echo $insert[$k]['lesson_id'] . ': ' . $insert[$k]['learning_outcome_id'] . '<br />';
			}
		}
		// echo '<pre>';
		// print_r($insert);
		// echo '</pre>';
		DB::table('learning_outcome_lesson')->upsert(
			$insert,
			['lesson_id', 'learning_outcome_id']
		);
	}

	/**
	* Migrate Media Types
	*/
	public function migrate_media_types()
	{
		$types = DB::table('_import_media')
			->select('media_extension')
			->distinct()
			->get();
		echo '<div class="mb-8">';
		echo '<a href="/admin/data-process-fg4390skamlfg33204">Back to Data Processing Page</a>';
		echo '</div>';
		/* need to add to this if new types emerge */
		$mime_types = [
			'mp3' => 'audio/mpeg',
			'mp4' => 'video/mp4',
			'wav' => 'audio/wav',
			'pdf' => 'application/pdf',
			'png' => 'image/png',
			'jpg' => 'image/jpeg',
			'jpeg' => 'image/jpeg',
			'svg' => 'image/svg+xml'
		];
		$insert = [];
		foreach ($types as $k => $v) {
			if ( isset($mime_types[$v->media_extension])) {
				$mime_type = $mime_types[$v->media_extension];
				$insert[$k]['type'] = $mime_type;
				$insert[$k]['created_at'] = date('Y-m-d H:i:s');
				$insert[$k]['updated_at'] = date('Y-m-d H:i:s');
				echo $insert[$k]['type'] . '<br />';
			}
		}
		// DB::table('media_types')->upsert(
		// 	$insert,
		// 	['name', 'guid']
		// );
	}

	/**
	 * TODO: Lesson Media lookups
	 */
 	/**
 	* Migrate Lesson Materials
 	*/
 	public function migrate_unit_week_lessons()
 	{
 		$items = DB::table('_import_weekly_lessons')
 			->get();
 		$unit_weeks = $this->unit_week_array();
 		$lessons = $this->lesson_array();
 		//return view('data-process-list-activities', ['data' => $data]);
 		echo '<div class="mb-8">';
 		echo '<a href="/admin/data-process-fg4390skamlfg33204">Back to Data Processing Page</a>';
 		echo '</div>';
 		$insert = [];
 		foreach ($items as $k => $v) {
			$uw_guid = $this->format_guid($v->UnitWeekId);
			$uw_id = isset($unit_weeks[$uw_guid]) ? $unit_weeks[$uw_guid] : null;
 			$l_guid = $this->format_guid($v->LessonId);
 			$l_id = isset($lessons[$l_guid]) ? $lessons[$l_guid] : null;
 			if ($l_id && $uw_id) {
 				$insert[$k]['unit_week_id'] = $uw_id;
 				$insert[$k]['lesson_id'] = $l_id;
 				$insert[$k]['is_active'] = 1;
 				$insert[$k]['created_at'] = date('Y-m-d H:i:s');
 				$insert[$k]['updated_at'] = date('Y-m-d H:i:s');
 				echo $insert[$k]['unit_week_id'] . ': ' . $insert[$k]['lesson_id'] . '<br />';
 			}
 		}
 		// echo '<pre>';
 		// print_r($insert);
 		// echo '</pre>';
 		DB::table('lesson_unit_week')->upsert(
 			$insert,
 			['lesson_id', 'unit_week_id']
 		);
 	}

	/**
	* Migrate Media
	*/
	public function migrate_media()
	{
		$items = $this->get_media_items();
		$media_types = $this->media_type_array();

		//return view('data-process-list-activities', ['data' => $data]);
		echo '<div class="mb-8">';
		echo '<a href="/admin/data-process-fg4390skamlfg33204">Back to Data Processing Page</a>';
		echo '</div>';
		$insert = [];
		foreach ($items as $k => $v) {
			$insert[$k]['guid'] = $this->format_guid($v['id']);
			$mt_type = $v['media_mime'];
			echo $mt_type;
			$mt_id = $media_types[$mt_type];
			$insert[$k]['media_type_id'] = $mt_id;
			$insert[$k]['name'] = $v['name'];
			$insert[$k]['file_name'] = $v['file_name'];
			$insert[$k]['is_deleted'] = 0;
			$insert[$k]['created_at'] = date('Y-m-d H:i:s');
			$insert[$k]['updated_at'] = date('Y-m-d H:i:s');
			echo $insert[$k]['media_type_id'] . ': ' . $insert[$k]['name'] . '<br />';
		}
		// echo '<pre>';
		// print_r($insert);
		// echo '</pre>';
		DB::table('media_library')->upsert(
			$insert,
			['guid']
		);
	}

	/**
 	* Migrate Lesson Media
 	*/
 	public function migrate_lesson_media()
 	{
 		$items = DB::table('_import_lesson_media')
 			->get();
 		$media_items = $this->media_array();
 		$lessons = $this->lesson_array();
 		//return view('data-process-list-activities', ['data' => $data]);
 		echo '<div class="mb-8">';
 		echo '<a href="/admin/data-process-fg4390skamlfg33204">Back to Data Processing Page</a>';
 		echo '</div>';
 		$insert = [];
 		foreach ($items as $k => $v) {
			$m_guid = $this->format_guid($v->MediaId);
			$m_id = isset($media_items[$m_guid]) ? $media_items[$m_guid] : null;
 			$l_guid = $this->format_guid($v->LessonId);
 			$l_id = isset($lessons[$l_guid]) ? $lessons[$l_guid] : null;
 			if ($l_id && $m_id) {
 				$insert[$k]['media_library_id'] = $m_id;
 				$insert[$k]['lesson_id'] = $l_id;
 				//$insert[$k]['created_at'] = date('Y-m-d H:i:s');
 				$insert[$k]['updated_at'] = date('Y-m-d H:i:s');
 				echo $insert[$k]['media_library_id'] . ': ' . $insert[$k]['lesson_id'] . '<br />';
 			}
 		}
 		// echo '<pre>';
 		// print_r($insert);
 		// echo '</pre>';
 		DB::table('lesson_media_library')->upsert(
 			$insert,
 			['lesson_id', 'media_library_id']
 		);
 	}

	/**
	* Get Vocabulary
	*/
	public function get_vocabulary()
	{
		/* using offset for larger tables to process only ~500 per run */
		/* NOTE: just change the offset number by 500 each run */
		$items = DB::table('_import_lessons')
			->select('Id', 'Vocabulary')
			->get();

		//return view('data-process-list-activities', ['data' => $data]);
		echo '<div class="mb-8">';
		echo '<a href="/admin/data-process-fg4390skamlfg33204">Back to Data Processing Page</a>';
		echo '</div>';
		$vocab_arr = [];
		foreach ($items as $k => $v) {
			//$insert[$k]['lesson_id'] = $this->format_guid($v->Id);
			//$insert[$k]['created_at'] = date('Y-m-d H:i:s');
			//$insert[$k]['updated_at'] = date('Y-m-d H:i:s');
			$vocab_string = str_replace('Key Vocabulary:', '', $v->Vocabulary);
			$vocab_string = str_replace('Key Vocabulary', '', $vocab_string);
			$vocabs = explode(',', $vocab_string);
			foreach ($vocabs as $vb) {
				if (strlen($vb) > 0) {
					$trimmed = trim($vb);
					if (strpos($trimmed, ')') == false) {
						$vocab_arr[$trimmed] = '';
					} else {
						$def_vocabs = explode(')', $vb);
						foreach ($def_vocabs as $dv) {
							$dv_trimmed = trim($dv) . ')';
							//echo $dv_trimmed . '<br/>';
							$dv_trimmed = ltrim($dv_trimmed, '.');
							//$dv_trimmed = rtrim($dv_trimmed, ')');
							if (
								$dv_trimmed !== '!)' &&
								$dv_trimmed !== '.)' &&
								$dv_trimmed !== ')'
							) {
								preg_match('/\((.*?)\)/', $dv_trimmed, $parsed);
								if (isset($parsed[1])) {
									$first = preg_replace("/\([^)]+\)/",'',$dv_trimmed);
									$first = trim($first);
									$first = rtrim($first, ')');
									$vocab_arr[$first] = $parsed[1];
								} else {
									$vocab_arr[$dv_trimmed] = '';
								}
							}
						}
					}
				}
			}
		}

		// print '<pre>';
		// ksort($vocab_arr);
		// print_r($vocab_arr);
		// print '</pre>';

		return $vocab_arr;
	}

	/**
	* Get Vocabulary
	*/
	public function parse_vocab_string($vocab_string)
	{
		$vocab_arr = array();
		$vocab_string = str_replace('Key Vocabulary:', '', $vocab_string);
		$vocab_string = str_replace('Key Vocabulary', '', $vocab_string);
		$vocabs = explode(',', $vocab_string);
		foreach ($vocabs as $vb) {
			if (strlen($vb) > 0) {
				$trimmed = trim($vb);
				if (strpos($trimmed, ')') == false) {
					$vocab_arr[$trimmed] = '';
				} else {
					$def_vocabs = explode(')', $vb);
					foreach ($def_vocabs as $dv) {
						$dv_trimmed = trim($dv) . ')';
						//echo $dv_trimmed . '<br/>';
						$dv_trimmed = ltrim($dv_trimmed, '.');
						//$dv_trimmed = rtrim($dv_trimmed, ')');
						if (
							$dv_trimmed !== '!)' &&
							$dv_trimmed !== '.)' &&
							$dv_trimmed !== ')'
						) {
							preg_match('/\((.*?)\)/', $dv_trimmed, $parsed);
							if (isset($parsed[1])) {
								$first = preg_replace("/\([^)]+\)/",'',$dv_trimmed);
								$first = trim($first);
								$first = rtrim($first, ')');
								$vocab_arr[$first] = $parsed[1];
							} else {
								$vocab_arr[$dv_trimmed] = '';
							}
						}
					}
				}
			}
		}

		return $vocab_arr;
	}

	/**
	* Migrate Vocabulary
	*/
	public function migrate_vocabulary()
	{
		$vocab_arr = $this->get_vocabulary();
		$vi=0;
		foreach ($vocab_arr as $vk => $vv) {
			$uuid = Uuid::uuid1()->toString();
			$insert[$vi]['guid'] = $this->format_guid($uuid);
			$insert[$vi]['is_active'] = 1;
			$insert[$vi]['word'] = $vk;
			$insert[$vi]['definition'] = $vv;
			$insert[$vi]['media_library_id'] = null;
			$insert[$vi]['created_at'] = date('Y-m-d H:i:s');
			$insert[$vi]['updated_at'] = date('Y-m-d H:i:s');
			$vi++;
		}
		// echo '<pre>';
		// print_r($insert);
		// echo '</pre>';
		DB::table('vocabularies')->upsert(
			$insert,
			['word']
		);
	}

	/**
	* Migrate Lesson Vocabulary
	*/
	public function migrate_lesson_vocabulary()
	{
		/* using offset for larger tables to process only ~500 per run */
		/* NOTE: just change the offset number by 500 each run */
		$items = DB::table('_import_lessons')
			->select('Id', 'Vocabulary')
			->get();

		//return view('data-process-list-activities', ['data' => $data]);
		echo '<div class="mb-8">';
		echo '<a href="/admin/data-process-fg4390skamlfg33204">Back to Data Processing Page</a>';
		echo '</div>';
		$vocabs = $this->vocabulary_array();
		$lessons = $this->lesson_array();
		$insert = [];
		foreach ($items as $k => $v) {
			$vocab_arr = $this->parse_vocab_string($v->Vocabulary);
			foreach ($vocab_arr as $vk => $vv) {
				$v_id = isset($vocabs[$vk]) ? $vocabs[$vk] : null;
				$l_guid = $this->format_guid($v->Id);
				$l_id = isset($lessons[$l_guid]) ? $lessons[$l_guid] : null;
				if ($l_id && $v_id) {
					$insert[$vk]['vocabulary_id'] = $v_id;
	 				$insert[$vk]['lesson_id'] = $l_id;
					$insert[$vk]['created_at'] = date('Y-m-d H:i:s');
	 				$insert[$vk]['updated_at'] = date('Y-m-d H:i:s');
					echo $insert[$vk]['vocabulary_id'] . ': ' . $insert[$vk]['lesson_id'] . '<br />';
				}
			}
		}

		DB::table('lesson_vocabulary')->upsert(
 			$insert,
 			['lesson_id', 'media_id']
 		);

		// echo '<pre>';
		// print_r($insert);
		// echo '</pre>';
		// DB::table('vocabularies')->upsert(
		// 	$insert,
		// 	['word']
		// );
	}

	/**
	* Get the Unit Week by Unit and Week number
	*/
	public function get_unit_week($unit_id, $week_num)
	{
		if ($unit_id && $week_num) {
			$items = DB::table('unit_weeks')
				->select('id')
				->where('unit_id', '=', $unit_id)
				->where('week_number', '=', $week_num)
				->limit(1)
				->get();
			if (isset($items[0]->id)) {
				return $items[0]->id;
			}
		}
		return false;
	}

	/**
	* Migrate Shopping Lists (newer migration to fix issue with missing data)
	*/
	public function migrate_shopping_lists_new()
	{
		$shopping_lists = DB::table('_import_unit_shopping_lists')
			->get();
		$categories = $this->category_array();
		$units = $this->unit_array();
		//return view('data-process-list-activities', ['data' => $data]);
		echo '<div class="mb-8">';
		echo '<a href="/admin/data-process-fg4390skamlfg33204">Back to Data Processing Page</a>';
		echo '</div>';
		$insert = [];
		echo 'count:' . count($shopping_lists) . '<br />';
		foreach ($shopping_lists as $k => $v) {
			$u_guid = $this->format_guid($v->UnitId);
			$u_id = isset($units[$u_guid]) ? $units[$u_guid] : null;
			$week_num = $v->ListWeek;
			$uw_id = $this->get_unit_week($u_id, $week_num);
			$c_guid = $v->ListLesson;
			$c_id = isset($categories[$c_guid]) ? $categories[$c_guid] : null;
			//echo 'c_id: ' . $c_id . '::uw_id: ' . $uw_id . '<br />';
			if ($c_id && $uw_id) {
				$insert[$k]['guid'] = $this->format_guid($v->Id);
				$insert[$k]['unit_week_id'] = $uw_id;
				$insert[$k]['category_id'] = $c_id;
				$insert[$k]['is_active'] = 1;
				$insert[$k]['created_at'] = date('Y-m-d H:i:s');
				$insert[$k]['updated_at'] = date('Y-m-d H:i:s');
				echo $insert[$k]['unit_week_id'] . ': ' . $insert[$k]['category_id'] . '<br />';
				
			}
		}
		// echo '<pre>';
		// print_r($insert);
		// echo '</pre>';
		DB::table('unit_week_shopping_lists')->upsert(
			$insert,
			['guid']
		);
	}

	/**
	* Migrate Shopping List Materials (newer migration to fix issue with missing data)
	*/
	public function migrate_shopping_list_materials_new()
	{
		$items = DB::table('_import_shopping_list_materials')
			->get();
		$materials = $this->material_array();
		$shopping_lists = $this->shopping_list_array_new();

		//return view('data-process-list-activities', ['data' => $data]);
		echo '<div class="mb-8">';
		echo '<a href="/admin/data-process-fg4390skamlfg33204">Back to Data Processing Page</a>';
		echo '</div>';
		$insert = [];
		foreach ($items as $k => $v) {
			$m_guid = $this->format_guid($v->MaterialId);
			$m_id = isset($materials[$m_guid]) ? $materials[$m_guid] : null;
			$sl_guid = $this->format_guid($v->UnitShoppingListId);
			$sl_id = isset($shopping_lists[$sl_guid]) ? $shopping_lists[$sl_guid] : null;
			if ($m_id && $sl_id) {
				$insert[$k]['material_id'] = $m_id;
				$insert[$k]['unit_week_shopping_list_id'] = $sl_id;
				$insert[$k]['created_at'] = date('Y-m-d H:i:s');
				$insert[$k]['updated_at'] = date('Y-m-d H:i:s');
				echo $insert[$k]['material_id'] . ': ' . $insert[$k]['unit_week_shopping_list_id'] . '<br />';
			}
		}
		// echo '<pre>';
		// print_r($insert);
		// echo '</pre>';
		DB::table('materials_unit_week_shopping_lists')->upsert(
			$insert,
			['material_id', 'unit_shopping_list_id']
		);
	}
}
