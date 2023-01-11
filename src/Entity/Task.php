<?php

namespace App\Entity;

use App\Repository\TaskRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TaskRepository::class)]
class Task
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $Name = null;

    #[ORM\ManyToOne(inversedBy: 'tasks')]
    private ?User $Author = null;

    #[ORM\OneToMany(mappedBy: 'Task', targetEntity: Comment::class, orphanRemoval: true, cascade:['persist'])]
    private Collection $comments;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $Text = null;

    #[ORM\Column(length: 10)]
    private ?string $Accessibility = null;

    #[ORM\Column]
    private ?bool $Status = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $Created = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $Deadline = null;

    #[ORM\ManyToOne(inversedBy: 'Tasks', cascade:['persist'])]
    private ?Company $company = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $Contractor = null;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
    }

    

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->Name;
    }

    public function setName(string $Name): self
    {
        $this->Name = $Name;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->Author;
    }

    public function setAuthor(?User $Author): self
    {
        $this->Author = $Author;

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setTask($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getTask() === $this) {
                $comment->setTask(null);
            }
        }

        return $this;
    }



    public function getText(): ?string
    {
        return $this->Text;
    }

    public function setText(?string $Text): self
    {
        $this->Text = $Text;

        return $this;
    }

    public function getAccessibility(): ?string
    {
        return $this->Accessibility;
    }

    public function setAccessibility(string $Accessibility): self
    {
        $this->Accessibility = $Accessibility;

        return $this;
    }

    public function isStatus(): ?bool
    {
        return $this->Status;
    }

    public function setStatus(bool $Status): self
    {
        $this->Status = $Status;

        return $this;
    }

    public function getCreated(): ?\DateTimeInterface
    {
        return $this->Created;
    }

    public function setCreated(\DateTimeInterface $Created): self
    {
        $this->Created = $Created;

        return $this;
    }

    public function getDeadline(): ?\DateTimeInterface
    {
        return $this->Deadline;
    }

    public function setDeadline(?\DateTimeInterface $Deadline): self
    {
        $this->Deadline = $Deadline;

        return $this;
    }

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): self
    {
        $this->company = $company;

        return $this;
    }

    public function getContractor(): ?string
    {
        return $this->Contractor;
    }

    public function setContractor(?string $Contractor): self
    {
        $this->Contractor = $Contractor;

        return $this;
    }

}

