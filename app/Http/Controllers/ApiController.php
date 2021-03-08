<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\ApiHelperTrait as ApiHelper;
use App\Models\Categories;
use App\Models\PostBoxes;
use App\Models\SubTask;
use App\Models\Task;
use Carbon\Carbon;

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
        $responseArr['msg'] = "PostBox deleted successfully";
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
        $responseArr = [];
        $categoryCount = Categories::count();
        $postBoxCount = PostBoxes::count();
        $postBox = PostBoxes::all();
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
        $responseArr['status'] = 200;
        $responseArr['type'] = 'success';
        $responseArr['msg'] = '';
        $responseArr['content'] = [
            'categoryCount' => $categoryCount,
            'postBoxCount' => $postBoxCount,
            'keywordCount' => count($uniqueKeywords)
        ];
        return response()->json($responseArr, 200);
    }

    public function allKeywords(Request $request)
    {
        $responseArr = [];
        if ($request->has('searchKeyword') && $request->get('searchKeyword') != '') {
            $postBox = PostBoxes::where('keywords', 'LIKE', '%' . $request->get('searchKeyword') . '%')->get();
        } else {
            $postBox = PostBoxes::all();
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
                        $arr['description'] = $v['description'];
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

}
