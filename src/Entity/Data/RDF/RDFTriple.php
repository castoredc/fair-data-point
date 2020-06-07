<?php
declare(strict_types=1);

// declare(strict_types=1);
//
// namespace App\Entity\Data\RDF;
//
// use App\Entity\Data\RDF\RDFTripleElement\RDFTripleElement;
// use App\Exception\InvalidTripleType;
// use Doctrine\ORM\Mapping as ORM;
//
// /**
//  * @ORM\Entity
//  * @ORM\InheritanceType("JOINED")
//  * @ORM\Table(name="rdf_triple")
//  */
// class RDFTriple
// {
//     /**
//      * @ORM\Id
//      * @ORM\Column(type="guid", length=190)
//      * @ORM\GeneratedValue(strategy="UUID")
//      *
//      * @var string
//      */
//     private $id;
//
//     /**
//      * @ORM\ManyToOne(targetEntity="RDFDistributionModule", inversedBy="triples",cascade={"persist"})
//      * @ORM\JoinColumn(name="module", referencedColumnName="id", nullable=false)
//      *
//      * @var RDFDistributionModule
//      */
//     private $module;
//
//     /**
//      * @ORM\OneToOne(targetEntity="App\Entity\Data\RDF\RDFTripleElement\RDFTripleElement", cascade={"persist"})
//      * @ORM\JoinColumn(name="subject", referencedColumnName="id", nullable=false)
//      *
//      * @var RDFTripleElement
//      */
//     private $subject;
//
//     /**
//      * @ORM\OneToOne(targetEntity="App\Entity\Data\RDF\RDFTripleElement\RDFTripleElement", cascade={"persist"})
//      * @ORM\JoinColumn(name="predicate", referencedColumnName="id", nullable=false)
//      *
//      * @var RDFTripleElement
//      */
//     private $predicate;
//
//     /**
//      * @ORM\OneToOne(targetEntity="App\Entity\Data\RDF\RDFTripleElement\RDFTripleElement", cascade={"persist"})
//      * @ORM\JoinColumn(name="object", referencedColumnName="id", nullable=false)
//      *
//      * @var RDFTripleElement
//      */
//     private $object;
//
//     public function __construct(RDFDistributionModule $module, RDFTripleSubject $subject, RDFTriplePredicate $predicate, RDFTripleObject $object)
//     {
//         if (! ($subject instanceof RDFTripleElement) || ! ($predicate instanceof RDFTripleElement) || ! ($object instanceof RDFTripleElement)) {
//             throw new InvalidTripleType();
//         }
//
//         $this->module = $module;
//         $this->subject = $subject;
//         $this->predicate = $predicate;
//         $this->object = $object;
//     }
//
//     public function getModule(): RDFDistributionModule
//     {
//         return $this->module;
//     }
//
//     public function getId(): string
//     {
//         return $this->id;
//     }
//
//     public function getSubject(): RDFTripleElement
//     {
//         return $this->subject;
//     }
//
//     public function getPredicate(): RDFTripleElement
//     {
//         return $this->predicate;
//     }
//
//     public function getObject(): RDFTripleElement
//     {
//         return $this->object;
//     }
// }
