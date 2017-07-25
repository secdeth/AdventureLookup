<?php

namespace AppBundle\Form;

use AppBundle\Entity\Adventure;
use AppBundle\Entity\Author;
use AppBundle\Entity\Edition;
use AppBundle\Entity\Environment;
use AppBundle\Entity\Item;
use AppBundle\Entity\Monster;
use AppBundle\Entity\NPC;
use AppBundle\Entity\Publisher;
use AppBundle\Entity\Setting;
use Doctrine\Common\Collections\Collection;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Valid;

class AdventureType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'help' => 'The title of the adventure.',
                'required' => true,
            ])
            ->add('description', TextareaType::class, [
                'help' => 'Description of the module.',
                'required' => false,
                'attr' => [
                    'rows' => 10,
                ]
            ]);
        $this->createAppendableEntityCollection(
            $builder,
            'authors',
            Author::class,
            AuthorType::class,
            'addAuthor',
            'Authors',
            'Names of people with writing or story credits on the module. Do not include editors or designers.'
        );
        $builder
            ->add('edition', EntityType::class, [
                'help' => 'The system the game was designed for and the edition of that system if there is one.',
                'required' => false,
                'class' => Edition::class,
                'multiple' => false,
                'choice_label' => 'name',
                'label' => 'System / Edition',
                'attr' => [
                    'data-autocomplete' => true,
                ],
            ])
            ->add('environments', EntityType::class, [
                'help' => 'The different types of environments the module will take place in.',
                'required' => false,
                'class' => Environment::class,
                'multiple' => true,
                'choice_label' => 'name',
                'attr' => [
                    'data-autocomplete' => true,
                ],
            ]);
        $this->createAppendableEntityCollection(
            $builder,
            'items',
            Item::class,
            ItemType::class,
            'addItem',
            'Items',
            'The notable magic or non-magic items that are obtained in the module. Only include named items, don\'t include a +1 sword.'
        );
        $this->createAppendableEntityCollection(
            $builder,
            'npcs',
            NPC::class,
            NPCType::class,
            'addNpc',
            'NPCs',
            'Names of notable NPCs'
        );
        $builder
            ->add('publisher', EntityType::class, [
                'help' => 'Publisher of the module.',
                'required' => false,
                'class' => Publisher::class,
                'multiple' => false,
                'choice_label' => 'name',
                'attr' => [
                    'data-autocomplete' => true,
                ],
            ])
            ->add('setting', EntityType::class, [
                'help' => 'The narrative universe the module is set in.',
                'required' => false,
                'class' => Setting::class,
                'multiple' => false,
                'choice_label' => 'name',
                'attr' => [
                    'data-autocomplete' => true,
                ],
            ]);
        $this->createAppendableEntityCollection(
            $builder,
            'monsters',
            Monster::class,
            MonsterType::class,
            'addMonster',
            'Monsters and BBEGs',
            'The monsters and BBEGs featured in the module.'
        );
        $builder
            ->add('minStartingLevel', NumberType::class, [
                'help' => 'The minimum level characters are expected to be when taking part in the module.',
                'required' => false,
            ])
            ->add('maxStartingLevel', NumberType::class, [
                'help' => 'The maximum level characters are expected to be when taking part in the module.',
                'required' => false,
            ])
            ->add('startingLevelRange', TextType::class, [
                'required' => false,
                'help' => 'In case no min. / max. starting levels but rather low/medium/high are given.',
                'attr' => [
                    'data-autocomplete' => true,
                ],
            ])
            ->add('numPages', NumberType::class, [
                'required' => false,
                'help' => 'Total page count of all written material in the module or at least primary string.',
                'label' => 'Length (# of Pages)'
            ])
            ->add('foundIn', TextType::class, [
                'required' => false,
                'help' => 'The magazine, site, etc. the adventure can be found in.',
                'attr' => [
                    'data-autocomplete' => true,
                ],
            ])
            ->add('partOf', TextType::class, [
                'required' => false,
                'help' => 'The series of adventures that the module is a part of, if applicable.',
                'attr' => [
                    'data-autocomplete' => true,
                ],
            ])
            ->add('link', UrlType::class, [
                'required' => false,
                'help' => 'Links to legitimate sites where the module can be procured.'
            ])
            ->add('thumbnailUrl', UrlType::class, [
                'required' => false,
                'help' => 'URL of the thumbnail image.'
            ])
            ->add('soloable', ChoiceType::class, [
                'help' => 'Whether or not this is suited to be played solo.',
                'required' => false,
                'choices' => [
                    'Yes' => true,
                    'No' => false,
                ],
                'placeholder' => 'Unknown',
                'label' => 'Suitable for Solo Play',
                'expanded' => true,
            ])
            ->add('pregeneratedCharacters', ChoiceType::class, [
                'help' => 'Whether or not this contains character sheets.',
                'required' => false,
                'choices' => [
                    'Yes' => true,
                    'No' => false,
                ],
                'placeholder' => 'Unknown',
                'label' => 'Includes Pregenerated Characters',
                'expanded' => true,
            ])
            ->add('tacticalMaps', ChoiceType::class, [
                'help' => 'Whether or not tactical maps are provided.',
                'required' => false,
                'choices' => [
                    'Yes' => true,
                    'No' => false,
                ],
                'placeholder' => 'Unknown',
                'label' => 'Tactical Maps',
                'expanded' => true,
            ])
            ->add('handouts', ChoiceType::class, [
                'help' => 'Whether or not handouts are provided.',
                'required' => false,
                'choices' => [
                    'Yes' => true,
                    'No' => false,
                ],
                'placeholder' => 'Unknown',
                'label' => 'Handouts',
                'expanded' => true,
            ])
        ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Adventure::class
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_adventure';
    }

    private function createAppendableEntityCollection(FormBuilderInterface $builder, string $fieldName, string $entity, string $form, string $method, string $title, string $help)
    {
        $builder
            // First create the normal dropdown for existing entities
            ->add($fieldName, EntityType::class, [
                'help' => $help,
                'required' => false,
                'class' => $entity,
                'multiple' => true,
                'choice_label' => 'name',
                'label' => $title,
            ])
            ->get($fieldName)
            // Drop any submitted options starting with 'n'.
            // This drops all new entities from the dropdown.
            // Existing entities will always have numeric ids and therefore not be dropped.
            ->addViewTransformer(new CallbackTransformer(function ($data) { return $data; }, function ($choices) {
                return array_filter($choices, function ($choice) {
                    return $choice[0] !== 'n';
                });
            }));
        $builder
            // Add an empty collection of entities.
            // This will hold all newly created entities.
            ->add($fieldName . '-new', CollectionType::class, [
                'entry_type' => $form,
                'allow_add' => true,
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'style' => 'background: #cdcdcd'
                ],
                'label' => 'New ' . $title . ' (added to list above)',
                'entry_options' => [
                    'label_attr' => [
                        'class' => 'd-none',
                    ]
                ],
                'constraints' => [
                    new Valid()
                ]
            ])
            // Once the form is submitted and validation has passed, associate the new entities
            // with the adventure. They will be persisted once the changes to the adventure are
            // added to the database.
            ->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $formEvent) use ($fieldName, $method) {
                /** @var Adventure $adventure */
                $adventure = $formEvent->getData();
                /** @var Monster[]|Collection $newEntities */
                $newEntities = $formEvent->getForm()->get($fieldName . '-new')->getData();
                foreach ($newEntities as $entity) {
                    $adventure->$method($entity);
                }
            }, -200)
        ;
    }
}
