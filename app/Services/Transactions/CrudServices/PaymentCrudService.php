<?php

namespace App\Services\Transactions\CrudServices;

use App\Services\CrudService;
use App\Repositories\IRepository;
use App\Repositories\Repository;
use App\Models\User;
use App\Models\Transaction;
use App\Enum\ROLES;
use App\Enum\TRANSACTION_STATUS;
use Carbon\Carbon;
use App\Events\Transactions\PaymentCreated;
use App\Events\Transactions\PaymentDeleted;

class PaymentCrudService extends CrudService{

    private IRepository $userRepository;
    private IRepository $transactionRepository;
    private ?User $user;
    private ?Transaction $transaction;
    
    public function __construct()
    {
        parent::__construct("Payment");
        $this->userRepository = Repository::getRepository('User');
        $this->transactionRepository = Repository::getRepository('Transaction');
        
        $this->user = null;
        $this->transaction = null;
        

        //NOTE
        //remaining work on payment:
        //make delete payment on last payment only and will call balance totalpayment
    }

    public function isValidCreate(array $request, \stdClass &$output): bool
    {
       if(!parent::isValidCreate($request, $output)) return false;
       
       //transaction id must exist and transaction id must not have a paid or a void status
       if(isset($request['transaction_id'])){
           $this->transaction = $this->transactionRepository->getById($request['transaction_id']);
           if(is_null($this->transaction))
           {
                $output->Error = ['Invalid transaction id', 'معرف المعاملة غير صحيح']; 
                return false;
           }

           if(strcmp($this->transaction->status, TRANSACTION_STATUS::VOID->value) === 0)
           {
              $output->Error = ['Cannot make payment for a void transaction ', 'لا يمكن إجراء الدفع لمعاملة باطلة']; 
              return false;
           }

           if(strcmp($this->transaction->status, TRANSACTION_STATUS::PAID->value) === 0)
           {
              $output->Error = ['Transaction is closed, cannot make a payment', 'تم إغلاق المعاملة، لا يمكن إجراء الدفع']; 
              return false;
           }

       }
       
       

       //if user id  exists must be of role customer and verified
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

       
        //this payment amount + traction payment must be > transaction total amount
        $transactionTotalAmount = TransactionCrudService::getTransactionTotalAmount($this->transaction);
        if(($request['amount'] + $this->transaction->paid_amount) > $transactionTotalAmount)
        {
            $output->Error = ['payment amount is greater than amount needed to complete the transaction, amount needed to complete payment is: '  . $transactionTotalAmount- $this->transaction->paid_amount,

                              'مبلغ الدفع أكبر من المبلغ المطلوب لإكمال المعاملة، فإن المبلغ المطلوب لإكمال الدفع هو:' . $transactionTotalAmount- $this->transaction->paid_amount]; 
            return false;
        }  

       return true;
    }

    public function create(array $request ,\stdClass &$output) :void
    {
        if(!$this->isValidCreate($request, $output)) return;
        $related_objects = $request['related_objects'] ?? [];

        $request['payment_date']  = Carbon::now()->toDateString();

        if(!isset($request['user_id']))
        {
            $request['user_id'] = $this->transaction->user_id;
        }
        
        $this->entity = $this->repository->create($request, $related_objects);   
        $output->{$this->entityName} = $this->entity;

        PaymentCreated::dispatch($this->entity->transaction_id);
    }


    //cannot update a payment
    public function update(array $request ,\stdClass &$output) :void
    {
        $output->Error = ['payment cannot be updated', 'لا يمكن تحديث الدفع على المعاملة']; 
    }

    public function isValidDelete(array $request, \stdClass &$output): bool
    {
        
        $this->entity = $this->repository->getById($request['id'], ['transaction']);
       
        //payment must exist
        if(is_null($this->entity))
        {
            $output->Error = ['Invalid payment id', 'رقم عملية الدفع غير صالح']; 
            return false;
        }

        //payment related transaction must not be of status paid or void
        $this->transaction = $this->entity->transaction;

        if(strcmp($this->transaction->status, TRANSACTION_STATUS::VOID->value) === 0)
        {
           $output->Error = ['Cannot delete payment for a void transaction ', 'لا يمكن حذف الدفع لمعاملة باطلة']; 
           return false;
        }

        if(strcmp($this->transaction->status, TRANSACTION_STATUS::PAID->value) === 0)
        {
           $output->Error = ['Transaction is closed, cannot make delete payment', 'تم إغلاق المعاملة، ولا يمكن إجراء عملية حذف الدفع']; 
           return false;
        }

        //canot delete payment if payment have more than one            
        $TransactionLatestPayment = $this->repository->getTransactionLatestPayment($this->transaction->id);
        if($TransactionLatestPayment->id != $this->entity->id)
        {
            $output->Error = ['payment deletion is only allowed for latest payment on a transaction',
                    'يُسمح بحذف الدفعة فقط لآخر دفعة في المعاملة']; 
            return false;
        }
        
       
    
        return true;
    }

    public function delete(array $request ,\stdClass &$output) :void
    {
        if(!$this->isValidDelete($request, $output)) return;
        $this->repository->delete([$request['id']]);
        
        PaymentDeleted::dispatch($this->entity->transaction_id);
    }
    
}