<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Traits\ApiHelperTrait as ApiHelper;
use App\Models\Categories;
use App\Models\PostBoxes;
use App\Models\SubTask;
use App\Models\Task;
use App\Models\Note;
use App\Models\User;
use Carbon\Carbon;
use Session;
use Auth;
use Hash;
use Image;

class ApiController extends Controller
{
    use ApiHelper;

    public function __construct()
    {

    }

    public function addCaregory(Request $request)
    {
        $rules = [
            'name' => ['required']
        ];
        $messages = [
            'name.required' => 'Category name is required'
        ];
        $validation = $this->checkInputValidation($request->all(), $rules, $messages);
        if (!empty($validation)) {
            return response()->json($validation, $validation['status']);
        }

        $responseArr = [];
        $userId = 1;

        $categoryName = $request->input('name');
        $isExist = Categories::where('user_id', $userId)->where('name', $categoryName)->exists();
        if ($isExist) {
            $responseArr['status'] = 200;
            $responseArr['type'] = 'error';
            $responseArr['msg'] = 'Category name already exist';
            $responseArr['content'] = ['categoryName' => $categoryName];
            return response()->json($responseArr, 200);
        }

        $categories = new Categories;
        $categories->user_id = $userId;
        $categories->name = $request->input('name');
        $categories->description = $request->input('description');
        $categories->visibility = $request->input('visibility');
        $categories->save();
        $responseArr['status'] = 200;
        $responseArr['type'] = 'success';
        $responseArr['msg'] = 'Category has been created successfully';
        $responseArr['content'] = [];
        return response()->json($responseArr, 200);
    }

    public function allCategories(Request $request)
    {
        $responseArr = [];
        $userId = 1;
        $allCategories = Categories::where('user_id', $userId)->orderBy('id', 'desc');
        if ($request->has('categoryName') && $request->get('categoryName') != '') {
            $allCategories = $allCategories->where('name', 'LIKE', '%' . $request->get('categoryName') . '%');
        }
        if ($request->has('status') && is_array($request->get('status')) && !empty($request->get('status'))) {
            $allCategories = $allCategories->whereIn('status', $request->get('status'));
        }
        if ($request->has('pagination') && $request->get('pagination') != '' && $request->get('pagination') != 0) {
            $allCategories = $allCategories->paginate($request->get('pagination'));
        } else {
            $allCategories = $allCategories->get();
        }
        $postBoxCount = [];
        foreach ($allCategories as $category) {
            $postBoxCount[$category->id] = !empty($category->postBox) ? count($category->postBox) : 0;
        }
        $responseArr['status'] = 200;
        $responseArr['type'] = 'success';
        $responseArr['msg'] = 'My categories';
        $responseArr['content'] = [
            'myCategories' => $allCategories,
            'postBoxCount' => $postBoxCount
        ];
        return response()->json($responseArr, 200);
    }

    public function deleteCategory(Request $request)
    {
        $responseArr = [];
        $category = Categories::find($request->input('categoryId'));
        $categoryName = $category->name;
        $category->delete();
        $responseArr['status'] = 200;
        $responseArr['type'] = 'success';
        $responseArr['msg'] = "Category '" . $categoryName . "' - deleted successfully";
        $responseArr['content'] = [];
        return response()->json($responseArr, 200);
    }

    public function changeCategoryStatus(Request $request)
    {
        $responseArr = [];
        if ($this->changeStatus('category', $request->input('id'), $request->input('status'))) {
            $responseArr['status'] = 200;
            $responseArr['type'] = 'success';
            $responseArr['msg'] = "Category status has been changed successfully";
            $responseArr['content'] = [];
        }
        return response()->json($responseArr, 200);
    }

    public function changeCategoryVisibility(Request $request)
    {
        $responseArr = [];
        if ($this->changeVisibility('category', $request->input('id'), $request->input('visibility'))) {
            $responseArr['status'] = 200;
            $responseArr['type'] = 'success';
            $responseArr['msg'] = "Category visibility has been changed successfully";
            $responseArr['content'] = [];
        }
        return response()->json($responseArr, 200);
    }

    public function bulkDeleteCategory(Request $request)
    {
        $responseArr = [];
        if ($this->bulkDelete('category', $request->input('ids'))) {
            $responseArr['status'] = 200;
            $responseArr['type'] = 'success';
            $responseArr['msg'] = "Categories has been deleted successfully";
            $responseArr['content'] = [];
        }
        return response()->json($responseArr, 200);
    }

    public function editCategory($id)
    {
        $responseArr = [];
        $responseArr['status'] = 404;
        $userId = 1;
        $categoryInfo = Categories::where('id', $id)->where('user_id', $userId)->first();
        if (!empty($categoryInfo)) {
            $responseArr['status'] = 200;
            $responseArr['type'] = 'success';
            $responseArr['msg'] = 'Category info';
            $responseArr['content'] = [
                'categoryInfo' => $categoryInfo
            ];
        }
        return response()->json($responseArr, $responseArr['status']);
    }

    public function updateCaregory(Request $request, $id)
    {
        $rules = [
            'name' => ['required']
        ];
        $messages = [
            'name.required' => 'Category name is required'
        ];
        $validation = $this->checkInputValidation($request->all(), $rules, $messages);
        if (!empty($validation)) {
            return response()->json($validation, $validation['status']);
        }

        $responseArr = [];
        $userId = 1;

        $categoryName = $request->input('name');
        $isExist = Categories::where('user_id', $userId)
            ->where('id', '!=', $id)
            ->where('name', $categoryName)
            ->exists();
        if ($isExist) {
            $responseArr['status'] = 200;
            $responseArr['type'] = 'error';
            $responseArr['msg'] = 'Category name already exist';
            $responseArr['content'] = ['categoryName' => $categoryName];
            return response()->json($responseArr, 200);
        }

        $category = Categories::where('id', $id)->where('user_id', $userId)->first();
        $category->name = $request->input('name');
        $category->description = $request->input('description');
        $category->visibility = $request->input('visibility');
        $category->status = $request->input('status');
        $category->save();
        $responseArr['status'] = 200;
        $responseArr['type'] = 'success';
        $responseArr['msg'] = 'Category has been updated successfully';
        $responseArr['content'] = [];
        return response()->json($responseArr, 200);
    }

    /** POST BOX **/

    public function addPostBox(Request $request)
    {
        $rules = [
            'title' => ['required'],
            'box_content' => ['required']
        ];
        $messages = [
            'title.required' => 'Post title is required',
            'box_content.required' => 'Post content is required'
        ];
        $validation = $this->checkInputValidation($request->all(), $rules, $messages);
        if (!empty($validation)) {
            return response()->json($validation, $validation['status']);
        }

        $responseArr = [];
        $userId = 1;

        $isExist = PostBoxes::where('user_id', $userId)
            ->where('title', $request->input('title'))
            ->exists();

        if ($isExist) {
            $responseArr['status'] = 200;
            $responseArr['type'] = 'error';
            $responseArr['msg'] = 'PostBox title already exist';
            $responseArr['content'] = [];
            return response()->json($responseArr, 200);
        }

        $postBoxes = new PostBoxes;
        $postBoxes->user_id = $userId;
        $postBoxes->title = $request->input('title');
        $postBoxes->keywords = $request->input('keywords');
        $postBoxes->category_id = $request->input('category_id');
        $postBoxes->status = $request->input('status');
        $postBoxes->visibility = $request->input('visibility');
        $postBoxes->box_content = trim(htmlentities(trim($request->input('box_content')), ENT_QUOTES));
        $postBoxes->save();
        $responseArr['status'] = 200;
        $responseArr['type'] = 'success';
        $responseArr['msg'] = 'PostBox has been created successfully';
        $responseArr['content'] = [];
        return response()->json($responseArr, 200);
    }

    public function allPostBoxes(Request $request)
    {
        $responseArr = [];
        $userId = 1;
        $allBoxes = PostBoxes::with(['postCategoryInfo'])
            ->where('user_id', $userId)
            ->orderBy('id', 'desc');
        
        if ($request->has('status') && is_array($request->get('status')) && !empty($request->get('status'))) {
            $allBoxes = $allBoxes->whereIn('status', $request->get('status'));
        }

        if ($request->has('search') && $request->get('search') != '') {
            $searchText = trim($request->get('search'));
            $allBoxes = $allBoxes->where(function($q) use($searchText) {
                $q->where('title', 'LIKE', '%' . $searchText . '%');
                $q->orWhere('keywords', 'LIKE', '%' . $searchText . '%');
            });
        }

        if ($request->has('category') && $request->get('category') != '' && $request->get('category') != 0) {
            $categoryId = trim($request->get('category'));
            $allBoxes = $allBoxes->where(function($q) use($categoryId) {
                $q->where('category_id', $categoryId);
            });
        }
        
        if ($request->has('pagination') && $request->get('pagination') != '' && $request->get('pagination') != 0) {
            $allBoxes = $allBoxes->paginate($request->get('pagination'));
        } else {
            $allBoxes = $allBoxes->get();
        }

        $allBoxes->transform(function($allBoxes) {
            $allBoxes->box_content = html_entity_decode(trim($allBoxes->box_content), ENT_QUOTES);
            $allBoxes->keywords = explode(',' , trim($allBoxes->keywords));
            $allBoxes->post_at = Carbon::parse($allBoxes->created_at)->format('d F, Y');

            if (isset($allBoxes->postCategoryInfo) && !empty($allBoxes->postCategoryInfo)) {
                $allBoxes->category_name = $allBoxes->postCategoryInfo->name;
                $allBoxes->category_id = $allBoxes->postCategoryInfo->id;
            }
            return $allBoxes;
        });
        
        $responseArr['status'] = 200;
        $responseArr['type'] = 'success';
        $responseArr['msg'] = 'My postboxes';
        $responseArr['content'] = [
            'myPostBoxes' => $allBoxes
        ];
        return response()->json($responseArr, 200);
    }

    public function changePostBoxStatus(Request $request)
    {
        $responseArr = [];
        if ($this->changeStatus('postbox', $request->input('id'), $request->input('status'))) {
            $responseArr['status'] = 200;
            $responseArr['type'] = 'success';
            $responseArr['msg'] = "PostBox status has been changed successfully";
            $responseArr['content'] = [];
        }
        return response()->json($responseArr, 200);
    }

    public function deletePostBox(Request $request)
    {
        $responseArr = [];
        $postbox = PostBoxes::find($request->input('postBoxId'));
        $postbox->delete();
        $responseArr['status'] = 200;
        $responseArr['type'] = 'success';
        $responseArr['msg'] = "PostBox has been deleted successfully";
        $responseArr['content'] = [];
        return response()->json($responseArr, 200);
    }

    public function editPostBox($id)
    {
        $responseArr = [];
        $responseArr['status'] = 404;
        $userId = 1;
        $postBoxInfo = PostBoxes::where('id', $id)->where('user_id', $userId)->first();
        if (!empty($postBoxInfo)) {
            $postBoxInfo->box_content = html_entity_decode(trim($postBoxInfo->box_content), ENT_QUOTES);
            $responseArr['status'] = 200;
            $responseArr['type'] = 'success';
            $responseArr['msg'] = 'PostBox info';
            $responseArr['content'] = [
                'postBoxInfo' => $postBoxInfo
            ];
        }
        return response()->json($responseArr, $responseArr['status']);
    }

    public function updatePostBox(Request $request, $id)
    {
        $rules = [
            'title' => ['required'],
            'box_content' => ['required']
        ];
        $messages = [
            'title.required' => 'Post title is required',
            'box_content.required' => 'Post content is required'
        ];
        $validation = $this->checkInputValidation($request->all(), $rules, $messages);
        if (!empty($validation)) {
            return response()->json($validation, $validation['status']);
        }

        $responseArr = [];
        $userId = 1;

        $isExist = PostBoxes::where('user_id', $userId)
            ->where('id', '!=', $id)
            ->where('title', $request->input('title'))
            ->exists();

        if ($isExist) {
            $responseArr['status'] = 200;
            $responseArr['type'] = 'error';
            $responseArr['msg'] = 'PostBox title already exist';
            $responseArr['content'] = [];
            return response()->json($responseArr, 200);
        }

        $postBoxes = PostBoxes::find($id);
        $postBoxes->title = $request->input('title');
        $postBoxes->keywords = $request->input('keywords');
        $postBoxes->category_id = $request->input('category_id');
        $postBoxes->status = $request->input('status');
        $postBoxes->visibility = $request->input('visibility');
        $postBoxes->box_content = trim(htmlentities(trim($request->input('box_content')), ENT_QUOTES));
        $postBoxes->save();
        $responseArr['status'] = 200;
        $responseArr['type'] = 'success';
        $responseArr['msg'] = 'PostBox has been updated successfully';
        $responseArr['content'] = [];
        return response()->json($responseArr, 200);
    }

    public function bulkDeletePostBox(Request $request)
    {
        $responseArr = [];
        if ($this->bulkDelete('postbox', $request->input('ids'))) {
            $responseArr['status'] = 200;
            $responseArr['type'] = 'success';
            $responseArr['msg'] = "PostBoxes has been deleted successfully";
            $responseArr['content'] = [];
        }
        return response()->json($responseArr, 200);
    }

    public function getAllCounts()
    {
        $userId = 1;
        $responseArr = [];
        $categoryCount = Categories::where('user_id', $userId)->count();
        $postBoxCount = PostBoxes::where('user_id', $userId)->count();
        $postBox = PostBoxes::where('user_id', $userId)->get();
        $keywordsArr = [];
        if (count($postBox)) {
            foreach ($postBox as $v) {
                if ($v->keywords != '') {
                    $explodeKeywords = explode(',', $v->keywords);
                    if (is_array($explodeKeywords) && !empty($explodeKeywords)) {
                        foreach ($explodeKeywords as $keyword) {
                            array_push($keywordsArr, trim($keyword));
                        }
                    }
                }
            }
        }
        $uniqueKeywords = array_unique($keywordsArr);

        $tasksCount = Task::where('user_id', $userId)->where('status', '!=', 1)->count();
        $notesCount = Note::where('user_id', $userId)->count();
        $responseArr['status'] = 200;
        $responseArr['type'] = 'success';
        $responseArr['msg'] = '';
        $responseArr['content'] = [
            'categoryCount' => $categoryCount,
            'postBoxCount' => $postBoxCount,
            'keywordCount' => count($uniqueKeywords),
            'tasksCount' => $tasksCount,
            'notesCount' => $notesCount
        ];
        return response()->json($responseArr, 200);
    }

    public function allKeywords(Request $request)
    {
        $userId = 1;
        $responseArr = [];
        if ($request->has('searchKeyword') && $request->get('searchKeyword') != '') {
            $postBox = PostBoxes::where('user_id', $userId)->where('keywords', 'LIKE', '%' . $request->get('searchKeyword') . '%')->get();
        } else {
            $postBox = PostBoxes::where('user_id', $userId)->get();
        }
    
        $keywordsArr = [];
        if (count($postBox)) {
            foreach ($postBox as $v) {
                if ($v->keywords != '') {
                    $explodeKeywords = explode(',', $v->keywords);
                    if (is_array($explodeKeywords) && !empty($explodeKeywords)) {
                        foreach ($explodeKeywords as $keyword) {
                            array_push($keywordsArr, trim($keyword));
                        }
                    }
                }
            }
        }
        $keyWords = [];
        $uniqueKeywords = array_count_values($keywordsArr);
        if (!empty($uniqueKeywords)) {
            foreach ($uniqueKeywords as $k => $v) {
                $arr = [];
                $arr['keywordName'] = $k;
                $arr['use'] = $v;
                array_push($keyWords, $arr);
            }
        }
        $responseArr['status'] = 200;
        $responseArr['type'] = 'success';
        $responseArr['msg'] = '';
        $responseArr['content'] = [
            'keywords' => $keyWords
        ];
        return response()->json($responseArr, 200);
    }

    /** TASK */

    public function getAllTasks(Request $request)
    {
        $responseArr = [];
        $userId = 1;
        $allTasks = Task::where('user_id', $userId)->orderBy('id', 'desc');
        if ($request->has('taskName') && $request->get('taskName') != '') {
            $allTasks = $allTasks->where('name', 'LIKE', '%' . $request->get('taskName') . '%');
        }
        if ($request->has('status') && is_array($request->get('status')) && !empty($request->get('status'))) {
            $allTasks = $allTasks->whereIn('status', $request->get('status'));
        } else if ($request->has('status') && !is_array($request->get('status')) && $request->get('status') != '') {
            $allTasks = $allTasks->where('status', $request->get('status'));
        } else {
            $allTasks = $allTasks->where('status', '!=', 1);
        }
        if ($request->has('pagination') && $request->get('pagination') != '' && $request->get('pagination') != 0) {
            $allTasks = $allTasks->paginate($request->get('pagination'));
        } else {
            $allTasks = $allTasks->get();
        }
        $subTaskCount = [];
        foreach ($allTasks as $tsk) {
            $subTaskCount[$tsk->id] = !empty($tsk->subTasks) ? count($tsk->subTasks) : 0;
        }
        $responseArr['status'] = 200;
        $responseArr['type'] = 'success';
        $responseArr['msg'] = 'My Tasks';
        $responseArr['content'] = [
            'myTaskList' => $allTasks,
            'subTaskCount' => $subTaskCount
        ];
        return response()->json($responseArr, 200);
    }

    public function createTask(Request $request)
    {
        $rules = [
            'taskName' => ['required']
        ];
        $messages = [
            'taskName.required' => 'Task name is required'
        ];
        $validation = $this->checkInputValidation($request->all(), $rules, $messages);
        if (!empty($validation)) {
            return response()->json($validation, $validation['status']);
        }

        $responseArr = [];
        $userId = 1;
        
        $task = new Task;
        $task->user_id = $userId;
        $task->name = $request->input('taskName');
        if ($task->save()) {
            if ($request->has('subTasks') && !empty($request->input('subTasks'))) {
                $subTasks = $request->input('subTasks');
                $subTasksArr = [];
                foreach ($subTasks as $v) {
                    if ($v['name'] != '') {
                        $arr = [];
                        $arr['task_id'] = $task->id;
                        $arr['name'] = $v['name'];
                        $arr['description'] = $v['description'] ?? '';
                        array_push($subTasksArr, $arr);
                    }
                }
                if (!empty($subTasksArr)) {
                    SubTask::insert($subTasksArr);
                }
            }
        }

        $responseArr['status'] = 200;
        $responseArr['type'] = 'success';
        $responseArr['msg'] = 'Task has been created successfully';
        $responseArr['content'] = [];
        return response()->json($responseArr, 200);
    }

    public function deleteTask(Request $request)
    {
        $responseArr = [];
        $task = Task::find($request->input('taskId'));
        $task->delete();
        SubTask::where('task_id', $request->input('taskId'))->delete();
        $responseArr['status'] = 200;
        $responseArr['type'] = 'success';
        $responseArr['msg'] = "Task has been deleted successfully";
        $responseArr['content'] = [];
        return response()->json($responseArr, 200);
    }

    public function bulkDeleteTask(Request $request)
    {
        $responseArr = [];
        if ($this->bulkDelete('tasks', $request->input('ids'))) {
            SubTask::whereIn('task_id', $request->input('ids'))->delete();
            $responseArr['status'] = 200;
            $responseArr['type'] = 'success';
            $responseArr['msg'] = "Tasks has been deleted successfully";
            $responseArr['content'] = [];
        }
        return response()->json($responseArr, 200);
    }

    public function changeStatus(Request $request)
    {
        $responseArr = [];
        $task = Task::find($request->input('id'));
        if (!empty($task)) {
            $task->status = $request->input('status');
            if ($request->input('status') == 0) {
                $task->task_percentage = 0;
            }
            if ($request->input('status') == 1) {
                $task->task_percentage = 100;
            }
            if ($request->input('status') == 2) {
                $task->task_percentage = 10;
            }
            $task->save();
        }
        $responseArr['status'] = 200;
        $responseArr['type'] = 'success';
        $responseArr['msg'] = "Task status has been changed successfully";
        $responseArr['content'] = [];
        return response()->json($responseArr, 200);
    }

    public function getTaskInfo(Request $request, $id)
    {
        $responseArr = [];
        $responseArr['status'] = 404;
        $userId = 1;
        $task = Task::where('id', $id)->where('user_id', $userId)->first();
        if (!empty($task)) {
            $task->description = trim(html_entity_decode(trim($task->description), ENT_QUOTES));
            $subTasks = SubTask::where('task_id', $id)->orderBy('id', 'asc')->get();
            $responseArr['status'] = 200;
            $responseArr['type'] = 'success';
            $responseArr['msg'] = 'Task info';
            $responseArr['content'] = [
                'taskInfo' => $task,
                'subTasksInfo' => $subTasks
            ];
        }
        return response()->json($responseArr, $responseArr['status']);
    }

    public function changeSubTaskStatus(Request $request)
    {
        $responseArr = [];
        $subtask = SubTask::find($request->input('id'));
        if (!empty($subtask)) {
            $subtask->status = $request->input('status');
            if ($request->input('status') == 0) {
                $subtask->subtask_percentage = 0;
            }
            if ($request->input('status') == 1) {
                $subtask->subtask_percentage = 100;
            }
            if ($request->input('status') == 2) {
                $subtask->subtask_percentage = 10;
            }
            $subtask->save();
        }
        $responseArr['status'] = 200;
        $responseArr['type'] = 'success';
        $responseArr['msg'] = "SubTask status has been changed successfully";
        $responseArr['content'] = [];
        return response()->json($responseArr, 200);
    }

    public function updateTask(Request $request, $id)
    {
        $rules = [
            'taskName' => ['required']
        ];
        $messages = [
            'taskName.required' => 'Task name is required'
        ];
        $validation = $this->checkInputValidation($request->all(), $rules, $messages);
        if (!empty($validation)) {
            return response()->json($validation, $validation['status']);
        }

        $responseArr = [];
        $userId = 1;
        
        $task = Task::find($id);
        $task->name = $request->input('taskName');
        $task->description = htmlentities($request->input('taskDescription'), ENT_QUOTES) ?? '';
        $task->status = $request->input('status');
        $task->task_percentage = $request->input('paraentTaskPercentage');
        if ($task->save()) {
            SubTask::where('task_id', $id)->delete();
            if ($request->has('subTasks') && !empty($request->input('subTasks'))) {
                $subTasks = $request->input('subTasks');
                $subTasksArr = [];
                foreach ($subTasks as $v) {
                    if ($v['name'] != '') {
                        $arr = [];
                        $arr['task_id'] = $task->id;
                        $arr['name'] = $v['name'];
                        $arr['description'] = $v['description'] ?? '';
                        $arr['status'] = $v['status'] ?? 0;
                        $arr['subtask_percentage'] = $v['subtask_percentage'] ?? 0;
                        array_push($subTasksArr, $arr);
                    }
                }
                if (!empty($subTasksArr)) {
                    SubTask::insert($subTasksArr);
                }
            }
        }

        $responseArr['status'] = 200;
        $responseArr['type'] = 'success';
        $responseArr['msg'] = 'Task has been updated successfully';
        $responseArr['content'] = [];
        return response()->json($responseArr, 200);
    }

    /** Notes */

    public function getNotes(Request $request)
    {
        $responseArr = [];
        $userId = 1;
        $allNotes = Note::where('user_id', $userId)->orderBy('id', 'desc');
        if ($request->has('noteTitle') && $request->get('noteTitle') != '') {
            $allNotes = $allNotes->where('title', 'LIKE', '%' . $request->get('noteTitle') . '%');
        }
        if ($request->has('status') && is_array($request->get('status')) && !empty($request->get('status'))) {
            $allNotes = $allNotes->whereIn('status', $request->get('status'));
        } else if ($request->has('status') && !is_array($request->get('status')) && $request->get('status') != '') {
            $allNotes = $allNotes->where('status', $request->get('status'));
        } else {
            $allNotes = $allNotes->whereNotNull('status');
        }
        if ($request->has('pagination') && $request->get('pagination') != '' && $request->get('pagination') != 0) {
            $allNotes = $allNotes->paginate($request->get('pagination'));
        } else {
            $allNotes = $allNotes->get();
        }
        
        $responseArr['status'] = 200;
        $responseArr['type'] = 'success';
        $responseArr['msg'] = 'My Notes';
        $responseArr['content'] = [
            'myNoteList' => $allNotes
        ];
        return response()->json($responseArr, 200);
    }

    public function createNote(Request $request)
    {
        $rules = [
            'title' => ['required']
        ];
        $messages = [
            'title.required' => 'Note title is required'
        ];
        $validation = $this->checkInputValidation($request->all(), $rules, $messages);
        if (!empty($validation)) {
            return response()->json($validation, $validation['status']);
        }

        $responseArr = [];
        $userId = 1;

        $noteTitle = $request->input('title');
        $isExist = Note::where('user_id', $userId)->where('title', $noteTitle)->exists();
        if ($isExist) {
            $responseArr['status'] = 200;
            $responseArr['type'] = 'error';
            $responseArr['msg'] = 'Note title already exist';
            $responseArr['content'] = ['noteName' => $noteTitle];
            return response()->json($responseArr, 200);
        }

        $note = new Note;
        $note->user_id = $userId;
        $note->title = $request->input('title');
        $note->slug = Str::slug($request->input('title'));
        $note->note_content = trim(html_entity_decode(trim($request->input('note_content')), ENT_QUOTES));
        $note->save();
        $responseArr['status'] = 200;
        $responseArr['type'] = 'success';
        $responseArr['msg'] = 'Note has been created successfully';
        $responseArr['content'] = [
            'note' => $note
        ];
        return response()->json($responseArr, 200);
    }

    public function getNoteInfoBySlug(Request $request, $slug)
    {
        $responseArr = [];
        $responseArr['status'] = 404;
        $userId = 1;
        $note = Note::where('slug', $slug)->where('user_id', $userId)->first();
        if (!empty($note)) {
            $note->note_content = trim(html_entity_decode(trim($note->note_content), ENT_QUOTES));
            $responseArr['status'] = 200;
            $responseArr['type'] = 'success';
            $responseArr['msg'] = 'Note info';
            $responseArr['content'] = [
                'noteInfo' => $note,
            ];
        }
        return response()->json($responseArr, $responseArr['status']);
    }

    public function getNoteInfoById(Request $request, $id)
    {
        $responseArr = [];
        $responseArr['status'] = 404;
        $userId = 1;
        $note = Note::where('id', $id)->where('user_id', $userId)->first();
        if (!empty($note)) {
            $note->note_content = trim(html_entity_decode(trim($note->note_content), ENT_QUOTES));
            $responseArr['status'] = 200;
            $responseArr['type'] = 'success';
            $responseArr['msg'] = 'Note info';
            $responseArr['content'] = [
                'noteInfo' => $note,
            ];
        }
        return response()->json($responseArr, $responseArr['status']);
    }

    public function updateNote(Request $request, $id)
    {
        $rules = [
            'title' => ['required']
        ];
        $messages = [
            'title.required' => 'Note title is required'
        ];
        $validation = $this->checkInputValidation($request->all(), $rules, $messages);
        if (!empty($validation)) {
            return response()->json($validation, $validation['status']);
        }

        $responseArr = [];
        $userId = 1;

        // Check note title exists or not
        $noteTitle = $request->input('title');
        $isExist = Note::where('user_id', $userId)
            ->where('id', '!=', $id)
            ->where('title', $noteTitle)
            ->exists();
        if ($isExist) {
            $responseArr['status'] = 200;
            $responseArr['type'] = 'error';
            $responseArr['msg'] = 'Note title already exist';
            $responseArr['content'] = ['noteTitle' => $noteTitle];
            return response()->json($responseArr, 200);
        }

        $note = Note::where('id', $id)->where('user_id', $userId)->first();
        $note->title = $request->input('title');

        // Check slug editable manually and exists or not
        if ($request->input('isSlugEditable')) {
            $slug = Str::slug($request->input('slugPart'));
            if ($this->isSlugExist('notes', $slug, $id)) {
                $responseArr['status'] = 200;
                $responseArr['type'] = 'error';
                $responseArr['msg'] = 'Note not saved! Slug already exist in system, Please try another.';
                return response()->json($responseArr, 200);
            }
            $note->slug = $slug;
        }

        $note->note_content = trim(html_entity_decode(trim($request->input('note_content')), ENT_QUOTES));
        $note->status = $request->input('status');
        $note->completed_percentage = $request->input('completed_percentage');
        $note->save();
        $responseArr['status'] = 200;
        $responseArr['type'] = 'success';
        $responseArr['msg'] = 'Note has been updated successfully';
        $responseArr['content'] = [
            'note' => $note
        ];
        return response()->json($responseArr, 200);
    }

    public function deleteNote(Request $request)
    {
        $responseArr = [];
        $note = Note::find($request->input('noteId'));
        $note->delete();
        $responseArr['status'] = 200;
        $responseArr['type'] = 'success';
        $responseArr['msg'] = "Note has been deleted successfully";
        $responseArr['content'] = [];
        return response()->json($responseArr, 200);
    }

    public function bulkDeleteNote(Request $request)
    {
        $responseArr = [];
        if ($this->bulkDelete('notes', $request->input('ids'))) {
            $responseArr['status'] = 200;
            $responseArr['type'] = 'success';
            $responseArr['msg'] = "Notes has been deleted successfully";
            $responseArr['content'] = [];
        }
        return response()->json($responseArr, 200);
    }

    // Auth
    public function signUp(Request $request)
    {
        $rules = [
            'name' => ['required'],
            'email' => ['bail', 'required', 'email', 'unique:users'],
            'username' => ['bail', 'required', 'unique:users'],
            'password' => ['required', 'min:8'],
        ];
        $messages = [
            'name.required' => 'Name is required',
            'email.required' => 'Email is required',
            'email.email' => 'Please enter valid email',
            'email.unique' => 'Email already exist, try another',
            'username.required' => 'Username is required',
            'username.unique' => 'Username already exist, try another',
            'password.required' => 'Password is required'
        ];
        $validation = $this->checkInputValidation($request->all(), $rules, $messages);
        if (!empty($validation)) {
            return response()->json($validation, $validation['status']);
        }

        $responseArr = [];

        $user = new User;
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->username = $request->input('username');
        $user->password = Hash::make($request->input('password'));
        $user->save();
        $responseArr['status'] = 200;
        $responseArr['type'] = 'success';
        $responseArr['msg'] = 'Account has been created successfully';
        $responseArr['content'] = [
            'user' => $user
        ];
        return response()->json($responseArr, 200);
    }

    public function signIn(Request $request)
    {
        $rules = [
            'userNameEmail' => ['required'],
            'password' => ['required'],
        ];
        $messages = [
            'userNameEmail.required' => 'Username or Email is required',
            'password.required' => 'Password is required'
        ];
        $validation = $this->checkInputValidation($request->all(), $rules, $messages);
        if (!empty($validation)) {
            return response()->json($validation, $validation['status']);
        }

        $requestData = $request->all();
        $responseArr = [];

        $isLoginSuccess = false;
        if (Auth::attempt(['email' => $requestData['userNameEmail'], 'password' => $requestData['password']])) {
            $isLoginSuccess = true;
        }
        if (!$isLoginSuccess && Auth::attempt(['username' => $requestData['userNameEmail'], 'password' => $requestData['password']])) {
            $isLoginSuccess = true;
        }
        
        $responseArr['status'] = 200;
        if (!$isLoginSuccess) {
            $responseArr['type'] = 'error';
            $responseArr['msg'] = 'Invalid login credentials';
            $responseArr['content'] = [];
            return response()->json($responseArr, 200);
        }

        $responseArr['type'] = 'success';
        $responseArr['msg'] = 'Hi, ' . Auth::user()->name . ' - welcome to your account';
        $responseArr['content'] = [
            'user' => Auth::user()
        ];
        return response()->json($responseArr, 200);
    }

    public function checkAuth(Request $request)
    {
        $responseArr = [];
        $responseArr['status'] = 401;
        $responseArr['content'] = [];
        if (Auth::check()) {
            $responseArr['status'] = 200;
            $responseArr['content'] = [
                'user' => Auth::user()
            ];
        }
        return response()->json($responseArr, $responseArr['status']);
    }

    public function signOut(Request $request)
    {
        Auth::logout();
        Session::flush();
        $responseArr = [];
        $responseArr['status'] = 200;
        $responseArr['type'] = 'success';
        $responseArr['msg'] = 'Successfully signout from your account';
        $responseArr['content'] = [];
        return response()->json($responseArr, 200);
    }

    // My Account
    public function changePassword(Request $request)
    {
        $rules = [
            'currentPassword' => ['required'],
            'newPassword' => ['bail', 'required', 'min:8'],
            'confirmPassword' => ['required', 'same:newPassword'],
        ];
        $messages = [
            'currentPassword.required' => 'Current password is required',
            'newPassword.required' => 'New password is required',
            'confirmPassword.required' => 'Confirm password is required',
            'confirmPassword.same' => 'Confirm password not match'
        ];
        $validation = $this->checkInputValidation($request->all(), $rules, $messages);
        if (!empty($validation)) {
            return response()->json($validation, $validation['status']);
        }

        $responseArr = [];
        $responseArr['status'] = 200;
        
        $user = User::find(Auth::user()->id);

        if (Hash::check($request->input('newPassword'), $user->password)) {
            $responseArr['type'] = 'error';
            $responseArr['msg'] = 'Sorry! Current password not match';
            $responseArr['content'] = [];
            return response()->json($responseArr, 200);
        }

        $user->password = Hash::make($request->input('newPassword'));
        $user->save();
        
        $responseArr['type'] = 'success';
        $responseArr['msg'] = 'Account password has been changed successfully';
        $responseArr['content'] = [];
        return response()->json($responseArr, 200);
    }

    public function changeUsername(Request $request)
    {
        $rules = [
            'username' => ['bail', 'required' ],
        ];
        $messages = [
            'username.required' => 'Username is required'
        ];
        $validation = $this->checkInputValidation($request->all(), $rules, $messages);
        if (!empty($validation)) {
            return response()->json($validation, $validation['status']);
        }

        $responseArr = [];
        $responseArr['status'] = 200;
        
        $ckUsernameisExist = User::where('username', $request->input('username'))->where('id', '!=', Auth::user()->id)->exists();
        if ($ckUsernameisExist) {
            $responseArr['type'] = 'error';
            $responseArr['msg'] = 'Sorry! Username already exist';
            $responseArr['content'] = [];
            return response()->json($responseArr, 200);
        }
        $user = User::find(Auth::user()->id);
        $user->username = $request->input('username');
        $user->save();
        
        $responseArr['type'] = 'success';
        $responseArr['msg'] = 'Username has been changed successfully';
        $responseArr['content'] = [];
        return response()->json($responseArr, 200);
    }

    public function changeEmail(Request $request)
    {
        $rules = [
            'email' => ['bail', 'required', 'email'],
        ];
        $messages = [
            'email.required' => 'Email is required',
            'email.email' => 'Please enter valid email',
        ];
        $validation = $this->checkInputValidation($request->all(), $rules, $messages);
        if (!empty($validation)) {
            return response()->json($validation, $validation['status']);
        }

        $responseArr = [];
        $responseArr['status'] = 200;
        
        $ckEmailExist = User::where('email', $request->input('email'))->where('id', '!=', Auth::user()->id)->exists();
        if ($ckEmailExist) {
            $responseArr['type'] = 'error';
            $responseArr['msg'] = 'Sorry! Email-id already exist';
            $responseArr['content'] = [];
            return response()->json($responseArr, 200);
        }
        $user = User::find(Auth::user()->id);
        $user->email = $request->input('email');
        $user->save();
        
        $responseArr['type'] = 'success';
        $responseArr['msg'] = 'Email-id has been changed successfully';
        $responseArr['content'] = [];
        return response()->json($responseArr, 200);
    }

    public function updateProfile(Request $request)
    {
        $rules = [
            'name' => ['required'],
            'avatar' => ['bail', 'image', 'mimes:jpeg,jpg,png', 'dimensions:min_width=300,min_height=180', 'max:1024'],
        ];
        $messages = [
            'name.required' => 'Name is required',
        ];
        $validation = $this->checkInputValidation($request->all(), $rules, $messages);
        if (!empty($validation)) {
            return response()->json($validation, $validation['status']);
        }

        $responseArr = [];
        $responseArr['status'] = 200;

        $user = User::find(Auth::user()->id);
        $user->name = $request->input('name');
        $user->mobile_number = $request->input('mobile_number') ?? '';
        $user->address = $request->input('address') ?? '';
        $user->sex = $request->input('sex') ?? '';

        if  ($request->hasFile('avatar')) {
            $img = $request->file('avatar');
            $real_path = $img->getRealPath();
            $file_orgname = $img->getClientOriginalName();
            $file_size = $img->getSize();
            $file_ext = strtolower($img->getClientOriginalExtension());
            $file_newname = "creative_syntax_product" . "_" . md5(microtime(TRUE) . rand(123, 999)) . "." . $file_ext;

            $destinationPath = public_path('/uploads');
            $thumb_path = $destinationPath."/thumb";
            
            $imgObj = Image::make($real_path);
            $imgObj->resize(150, NULL, function ($constraint) {
                $constraint->aspectRatio();
            })->save($thumb_path . '/' . $file_newname);

            $img->move($destinationPath, $file_newname);
            $user->profile_image = $file_newname;
        }

        $user->save();

        $responseArr['type'] = 'success';
        $responseArr['msg'] = 'Profile has been updated successfully';
        $responseArr['content'] = [
            'user' => $user
        ];
        return response()->json($responseArr, 200);
    }

    public function deleteProfileImage(Request $request)
    {
        $user = User::find(Auth::user()->id);
        $user->profile_image = null;
        $user->save();
        $responseArr = [];
        $responseArr['status'] = 200;
        $responseArr['type'] = 'success';
        $responseArr['msg'] = 'Profile image has been removed successfully';
        $responseArr['content'] = [
            'user' => $user
        ];
        return response()->json($responseArr, 200);
    }

    // Check Slug
    public function checkSlug(Request $request)
    {
        $responseArr = [];
        $responseArr['status'] = 200;
        $responseArr['type'] = 'success';

        $slug = $request->input('slug');
        $id = $request->input('id');
        
        if ($this->isSlugExist('notes', $slug, $id)) {
            $responseArr['type'] = 'error';
            $responseArr['msg'] = 'Slug already exist in system, Please try another.';
        }
        $responseArr['content'] = [];
        return response()->json($responseArr, $responseArr['status']);
    }

}
