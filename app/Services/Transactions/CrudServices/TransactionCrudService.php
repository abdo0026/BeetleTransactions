<?php

namespace App\Services\Transactions\CrudServices;

use App\Services\CrudService;
use App\Repositories\Repository;
use App\Models\User;
use App\Models\Categories;
use App\Models\SubCategories;
use App\Enum\ROLES;
USE APP\Enum\TRANSACTION_STATUS;
use Carbon\Carbon;

class TransactionCrudService extends CrudService{

    private Repository $userRepository;
    private Repository $categoryRepository;
    private Repository $subCategoryRepository;
    private ?User $user;
    private ?Categories $category;
    private ?SubCategories $subCategory;

    public function __construct()
    {
        parent::__construct("Transaction");
        $this->userRepository = Repository::getRepository('User');
        $this->categoryRepository = Repository::getRepository('Categories');
        $this->subCategoryRepository = Repository::getRepository('SubCategories');
        $this->user = null;
        $this->category = null;
        $this->subCategory = null;

        //NOTE
        //remaining work on trasactions:
        // make update bussines
        // make delete transaction by setting status to void (cannot void transactin if it has an payments or its status os paid)
    }

    public function isValidCreate(array $request, \stdClass &$output): bool
    {
       if(!parent::isValidCreate($request, $output)) return false;
       
       //user id must exists and must be of role customer
       if(isset($request['user_id']))
       {
            $this->user = $this->userRepository->getById($request['user_id'], ['roles', 'registerationValidation']);
            
            if(is_null($this->user)){
               $output->Error = ['Invalid user id', 'هوية مستخدم خاطئة']; 
               return false;
            }

            //user account must be verified
            if(!$this->user->registerationValidation->is_verified_email)
            {
               $output->Error = ['Cannot create transaction for un verified user', 'لا يمكن إنشاء معاملة لمستخدم لم يتم التحقق منه']; 
               return false;
            }

            //user must be a customer
            if(!$this->user->hasRole(ROLES::CUSTOMER->value))
            {
                $output->Error = ['User assigned is not a customer', 'المستخدم المعين ليس عميلاً']; 
                return false;
            }

        }

       
        //sub_category_id if exits must be related to above cathegory id
        if(isset($request['sub_category_id']))
        {
            $this->subCategory = $this->subCategoryRepository->getById($request['sub_category_id'], ['category']);
            if(is_null($this->subCategory)){
                $output->Error = ['Invalid sub category id', 'معرف الفئة الفرعية غير صالح']; 
                return false;
            }

            if($this->subCategory->category->id !== intval($request['category_id']))
            {
                $output->Error = ['sub category mentioned is nOt related to main category', 'الفئة الفرعية المذكورة لا تتعلق بالفئة الرئيسية']; 
                return false;
            }

        }else{

            $this->category = $this->userRepository->getById($request['category_id']);
                
            if(is_null($this->category)){
               $output->Error = ['Invalid category id', 'هوية الفئات خاطئة']; 
               return false;
            }
        }

       

       //due date must not be in past
       $request['due_date'] = Carbon::parse($request['due_date'])->toDateString();
       if($request['due_date'] < Carbon::now()->toDateString())
       {
            $output->Error = ['Due date is specified in the past', 'تم تحديد تاريخ الاستحقاق في الماضي']; 
            return false;
       }

       
       //user must not have any overdue or Outstanding transactions
       $openTransactionStatus = [
          TRANSACTION_STATUS::OUTSTANDING->value,
          TRANSACTION_STATUS::OVERDUE->value
       ];

       if($this->repository->isUserHasTrasactions($request['user_id'], $openTransactionStatus))
       {
        $output->Error = ['user has open transactions please close all before creating new transaction', 
                           'لدى المستخدم معاملات مفتوحة، يرجى إغلاق الكل قبل إنشاء معاملة جديدة']; 
        return false;
       }

       

       return true;
    }

    public function create(array $request ,\stdClass &$output) :void
    {
        if(!$this->isValidCreate($request, $output)) return;
        $related_objects = $request['related_objects'] ?? [];

        if(!isset($request['status']))
        {
            $request['status'] = TRANSACTION_STATUS::OUTSTANDING->value;
        }

        $request['admin_id'] = auth()->user()->id;
        
        $this->entity = $this->repository->create($request, $related_objects);   
        $output->{$this->entityName} = $this->entity;
    }
    
}