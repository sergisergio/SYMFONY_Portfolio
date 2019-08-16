<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ImageRepository")
 * @Vich\Uploadable
 */
class Image
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    private $name;
    /**
     * @var string
     *
     * @ORM\Column(name="extension", type="string", length=255, nullable=true)
     */
    private $extension;

    /**
     * @Vich\UploadableField(mapping="image", fileNameProperty="name")
     */
    private $file;
    /**
     * @var string
     */
    private $tempFilename;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $url;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $alt;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Projects", inversedBy="image", cascade={"persist", "remove"})
     */
    private $project;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $style;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getAlt(): ?string
    {
        return $this->alt;
    }

    public function setAlt(?string $alt): self
    {
        $this->alt = $alt;

        return $this;
    }

    public function getProject(): ?Projects
    {
        return $this->project;
    }

    public function setProject(?Projects $project): self
    {
        $this->project = $project;

        return $this;
    }

    public function getStyle(): ?string
    {
        return $this->style;
    }

    public function setStyle(?string $style): self
    {
        $this->style = $style;

        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getExtension(): string
    {
        return $this->extension;
    }

    /**
     * @param string $extension
     */
    public function setExtension(string $extension): void
    {
        $this->extension = $extension;
    }

    /**
     * @return File|null
     */
    public function getFile(): ?string
    {
        return $this->file;
    }

    /**
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $file
     */
    public function setFile(?File $file): void
    {
        $this->file = $file;

        if ($this->extension !== null) {
            $this->setTempFilename();
            $this->url = null;
            $this->alt = null;
        }
    }

    /**
     * @return string
     */
    public function getTempFilename(): string
    {
        return $this->tempFilename;
    }

    /**
     * @param string $tempFilename
     */
    public function setTempFilename()
    {
        $this->tempFilename = $this->name.'.'.$this->extension;
    }

    /**
     * @return string
     */
    public function getUploadDir(): string
    {
        return 'uploads/images';
    }
    /**
     * @return string
     */
    public function getUploadRootDir(): string
    {
        return __DIR__.'/../../public/'.$this->getUploadDir();
    }
    /**
     * @return string
     */
    public function getPath(): ?string
    {
        return $this->getUploadDir().'/'.$this->getName().'.'.$this->getExtension();
    }
}
