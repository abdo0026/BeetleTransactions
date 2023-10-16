<?php

namespace App\Services\Transactions\CrudServices;

use App\Services\CrudService;
use App\Repositories\Repository;
use App\Models\User;
use App\Models\Categories;
use App\Models\SubCategories;
use App\Models\Transaction;
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

       
       /*
       //due date must not be in past
       $request['due_date'] = Carbon::parse($request['due_date'])->toDateString();
       if($request['due_date'] < Carbon::now()->toDateString())
       {
            $output->Error = ['Due date is specified in the past', 'تم تحديد تاريخ الاستحقاق في الماضي']; 
            return false;
       }
        */
       
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
            
            if(Carbon::parse($request['due_date'])->toDateString() < Carbon::now()->toDateString()){
                $request['status'] = TRANSACTION_STATUS::OVERDUE->value;
            }
            
            
        }

        

        $request['admin_id'] = auth()->user()->id;
        
        $this->entity = $this->repository->create($request, $related_objects);   
        $output->{$this->entityName} = $this->entity;
    }



    public function isValidDelete(array $request, \stdClass &$output): bool
    {
        
        $this->entity = $this->repository->getById($request['id'], ['payments']);
       
        //transaction must exist
        if(is_null($this->entity))
        {
            $output->Error = ['Invalid transaction id', 'معرف المعاملة غير صحيح']; 
            return false;
        }

        //transaction must not be void or paid
        if(strcmp($this->entity->status, TRANSACTION_STATUS::VOID->value) === 0)
        {
              $output->Error = ['Transaction is already voided', 'تم إبطال المعاملة بالفعل']; 
              return false;
        }

        if(strcmp($this->entity->status, TRANSACTION_STATUS::PAID->value) === 0)
        {
            $output->Error = ['cannot void a closed transaction', 'لا يمكن إبطال معاملة مغلقة']; 
            return false;
        }

        
        
        //transaction must nnot have any payment od it
        if(sizeof($this->entity->payments) > 0)
        {
            $output->Error = ['cannot void transaction which have existing payments', 'لا يمكن إبطال المعاملة التي لها دفعات موجودة']; 
            return false;
        }
        
    
        return true;
    }

    public function delete(array $request ,\stdClass &$output) :void
    {
        if(!$this->isValidDelete($request, $output)) return;
        $output->{$this->entityName} = $this->repository->update($this->entity, ['status' => TRANSACTION_STATUS::VOID->value]);
    }

    public static function getTransactionTotalAmount(Transaction $transaction): float
    {
       $amount = $transaction->amount;
       return $transaction->is_vat_included ? $amount : 
               $amount += (($transaction->vat_percentage / 100) * $amount);

    }

    public function reCalculateTransactionPaidAmount(Transaction $transaction)
    {
           $totalPaidAmount = 0;
           
           $payments = $transaction->payments;
           if(sizeof($payments) > 0)
           {
              $totalPaidAmount = $payments->sum('amount');
           }

           $this->repository->update($transaction, ['paid_amount' => $totalPaidAmount]);
    }

    public function reCalculateTransactionStatus(Transaction $transaction)
    {
        $tolatAmount = self::getTransactionTotalAmount($transaction);
        $istotalAmountPaid = $transaction->paid_amount == $tolatAmount;
        
        //if total Amount is paid chnage status to paid
        if($istotalAmountPaid)
        {
            $this->repository->update($transaction, ['status' => TRANSACTION_STATUS::PAID->value]);
            return;
        }
         
        //transaction passed due
        if(strcmp($transaction->status, TRANSACTION_STATUS::OVERDUE->value) !== 0  &&$transaction->due_date < Carbon::now()->toDateString())
        {
            $this->repository->update($transaction, ['status' => TRANSACTION_STATUS::OVERDUE->value]);
            return;
        }

    }
    
}