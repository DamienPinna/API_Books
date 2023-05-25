<?php

namespace App\Entity;

use App\Repository\BookRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: BookRepository::class)]
class Book {
  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column]
  #[Groups("getBooks")]
  private ?int $id = null;

  #[ORM\Column(length: 255)]
  #[Groups("getBooks")]
  #[Assert\NotBlank(message: "Le titre du livre est obligatoire")]
  #[Assert\Length(
    min: 1,
    max: 255,
    minMessage: "Le titre doit faire au moins {{ limit }} caractères",
    maxMessage: "Le titre doit faire moins de {{ limit }} caractères",
  )]
  private ?string $title = null;

  #[ORM\Column(type: Types::TEXT, nullable: true)]
  #[Groups("getBooks")]
  private ?string $coverText = null;

  #[ORM\ManyToOne(inversedBy: 'books')]
  #[ORM\JoinColumn(onDelete: "CASCADE")]
  #[Groups("getBooks")]
  private ?Author $author = null;

  public function getId(): ?int {
    return $this->id;
  }

  public function getTitle(): ?string {
    return $this->title;
  }

  public function setTitle(string $title): self {
    $this->title = $title;
    return $this;
  }

  public function getCoverText(): ?string {
    return $this->coverText;
  }

  public function setCoverText(?string $coverText): self {
    $this->coverText = $coverText;
    return $this;
  }

  public function getAuthor(): ?Author {
    return $this->author;
  }

  public function setAuthor(?Author $author): self {
    $this->author = $author;
    return $this;
  }
}
