<?php

namespace App\Controller;

//use doctrine;
use DateTime;
use App\Entity\Task;
use App\Entity\User;
use App\Form\TaskType;
use App\Form\UserType;
use App\Entity\Comment;
use Psr\Log\NullLogger;
use App\Form\GetJobType;
use App\Form\CommentType;
use Doctrine\ORM\ORMSetup;
use Doctrine\ORM\EntityManager;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use App\Repository\CommentRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/taskList')]
class UserController extends AbstractController
{

    #[Route('/', name: 'app_index', methods: ['GET', 'POST'])]
    public function all(Request $request, UserRepository $userRepository, TaskRepository $taskRepository): Response
    {
        if($this->getUser()){

            $privateTasks=$taskRepository->findBy(['Author'=>$this->getUser()->getUserIdentifier()]);
            $companyTasks= $taskRepository->findBy(["company"=>$this->getUser()->getCompany()->getID()]);
        }
        else{
            $privateTasks=null;
            $companyTasks=null;
        }

        $publicTasks=$taskRepository->FindBy(["Accessibility"=>"PUBLIC"]);

       // $allTasks= $taskRepository->findAll();
        //$allUsers=$userRepository->findAll();    


        return $this->renderForm('user/index.html.twig', [
          'private'=> $privateTasks,
          'company' =>$companyTasks,
          'public' =>$publicTasks
        ]);
    }

    #[Route('/user', name: 'app_user', methods: ['GET', 'POST'])]
    public function index(ManagerRegistry $doctrine, UserRepository $userRepository,  TaskRepository $taskRepository, Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if($this->getUser()){
            $privateTasks=$taskRepository->findBy(['Author'=>$this->getUser()->getId()]);     

            
            
            
                }
        else {
            $privateTasks='no Tasks';
        }
    //$task=$this->getTask();
   // $formGetJob = $this->createForm(GetJobType::class, $task);
   // $formGetJob->handleRequest($request);
    
   
    // 
    //     $getJob=$formGetJob->getData();
    //     $task->setStatus($getJob->getStatus);
    //     if($getJob->getContractor->getValues()){
    //         $task->setContractor($this->getUser()->getEmail());
    //     }else{
    //         $task->setContractor(null);
    //     }
    // }
       // $taskRepository->save($task, true);

                
        return $this->render('user/user.html.twig', [
            'private' => $privateTasks,
            

        ]);
    }

    #[Route('/company', name: 'app_company', methods: ['GET'])]
    public function company(ManagerRegistry $doctrine, UserRepository $userRepository, TaskRepository $taskRepository): Response
    {
      //  $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if($this->getUser()){
            
        $company = $this->getUser()->getCompany()->getId(); 
        //dd($taskRepository);
        $tasks= $taskRepository->findBy(["company"=> $company]);
        
        }
        else {
            $tasks='no Tasks';
        }
                
        return $this->render('user/company.html.twig', [
            'tasks' => $tasks,

        ]);
    }

    #[Route('/{id}', name: 'app_user_show', methods: ['GET', 'POST'])]
    public function show(Task $task, UserRepository $userRepository, TaskRepository $taskRepository, CommentRepository $commentRepository, Request $request): Response
    {
        
        $loggedUser = $this->getUser()->getUserIdentifier();
        $user =$userRepository->findOneBy(['email'=>$loggedUser]);
        
        $comments=$task->getComments()->getValues();

        $formGetJob = $this->createForm(GetJobType::class, $task);
            $formGetJob->handleRequest($request);
                if ($formGetJob->isSubmitted() && $formGetJob->isValid()) {
                    dd($formGetJob->getData());
                }
       // $comments= $commentRepository->findBy(['Task'=>$task->getId()]);

       // dd($comments);

        
        return $this->render('user/show.html.twig', [
            'task' => $task,
            'user' => $user,
            'comments' =>$comments,       
            'getjob' => $formGetJob     
        ]);
    }


    #[Route('/{id}/comments', name: 'app_comments', methods: ['GET', 'POST'], priority:2)]
    public function showComments(Task $task, Request $request, TaskRepository $taskRepository, UserRepository $userRepository): Response
    {
    
        $loggedUser = $this->getUser()->getUserIdentifier();
        $user =$userRepository->findOneBy(['email'=>$loggedUser]);
        
        $comments=$task->getComments();
      
       // dd($task->getAuthor()->getEmail());
              
    
        return $this->render('user/comments.html.twig', [
            'comments' => $comments,
            'task' =>$task
           
        ]);
    }    
    
#[Route('/{id}/newcomment', name: 'app_new_comment', methods: ['GET', 'POST'], priority:2)]
public function newComment(Task $task, Request $request, TaskRepository $taskRepository, UserRepository $userRepository): Response
{
    $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
    $loggedUser = $this->getUser()->getUserIdentifier();
    $user =$userRepository->findOneBy(['email'=>$loggedUser]);

    $comments = $task->getComments();

    //dd($comments);

    $comment = new Comment();
    $formComment = $this->createForm(CommentType::class, $comment);
    $formComment->handleRequest($request);
  
   // dd($task);

   
    if ($formComment->isSubmitted() && $formComment->isValid()) {
        $comment=$formComment->getData();
        $comment->setCreated(new DateTime('now'));
        $task->addComment($comment);
        
        $taskRepository->save($task, true);

         return $this->redirectToRoute('app_user', [], Response::HTTP_SEE_OTHER);
    }

    return $this->render('user/new_comment.html.twig', [
        'comments' => $comments,
        'form' => $formComment,
        'task' => $task
    ]);
}

 
  


    #[Route('/newtask', name: 'app_new_task', methods: ['GET', 'POST'], priority:3)]
    public function newTask(Request $request, TaskRepository $taskRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $task = new Task();
        $formTask = $this->createForm(TaskType::class, $task);
        $formTask->handleRequest($request);

        if ($formTask->isSubmitted() && $formTask->isValid()) {

            $task->setAuthor($this->getUser());
            $task->setCreated(new DateTime('now'));           
            $task->setAccessibility($formTask->getData()->getAccessibility());
            
           
            if(($task->getAccessibility())=="COMPANY"){                
                $task->setCompany($this->getUser()->getCompany());            
            }
            
            if($formTask->getData()->getDeadline()<new DateTime('now')){
                $this->addFlash('notice', 'Niepoprawna data');

                return $this->redirectToRoute(('app_new_task'));
            }
            $taskRepository->save($task, true);

            return $this->redirectToRoute('app_user', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/new_task.html.twig', [
            'task' => $task,
            'form' => $formTask,
        ]);
    }


    

    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, UserRepository $userRepository): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userRepository->save($user, true);

            return $this->redirectToRoute('app_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_task_delete', methods: ['POST'])]
    public function delete(Task $task, TaskRepository $taskRepository): Response
    {

  //dd($this->isCsrfTokenValid('delete',$user->getId(), $request->request->get('_token')));
        //if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $taskRepository->remove($task, true);
      //  }

        return $this->redirectToRoute('app_index');
    }

    #[Route('/register', name: 'app_register_appp', methods: ['GET', 'POST'])]
    public function new(Request $request, UserRepository $userRepository): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

           
            $userRepository->save($user, true);

            return $this->redirectToRoute('app_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/register.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }


}