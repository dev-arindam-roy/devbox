<?php

namespace App\Traits;
use Validator;
use Log;
use DB;

trait ApiHelperTrait {

    public function checkInputValidation($formData, $rules, $messages)
    {
        $responseArr = [];
        $validation = Validator::make($formData, $rules, $messages);
        if ($validation->fails()) {
            $responseArr['status'] = 422;
            $responseArr['type'] = 'fieldValidationError';
            $responseArr['msg'] = 'Validation Errors';
            $validationErrors = $validation->errors();
            $validationErrorsArr = $validation->errors()->toArray();
            $errors = [];
            foreach($validationErrorsArr as $errs) {
                foreach($errs as $err) {
                    array_push($errors, $err);
                }
            }
            $responseArr['content'] = [
                'validationErrors' => $errors
            ];
        }
        return $responseArr;
    }

    public function changeStatus($tabName, $rowId, $status)
    {
        DB::table($tabName)->where('id', $rowId)->update(['status' => $status]);
        return true;
    }

    public function changeVisibility($tabName, $rowId, $visibility)
    {
        DB::table($tabName)->where('id', $rowId)->update(['visibility' => $visibility]);
        return true;
    }

    public function bulkDelete($tabName, $rowIds = [])
    {
        DB::table($tabName)->whereIn('id', $rowIds)->delete();
        return true;
    }

    public function isSlugExist($tabName, $slug, $id)
    {
        $isExist = DB::table($tabName)->where('slug', trim($slug))->where('id', '!=', $id)->exists();
        if ($isExist) {
            return true;
        }
        return false;
    }
}
