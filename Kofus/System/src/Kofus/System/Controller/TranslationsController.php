<?php
namespace Kofus\System\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;



class TranslationsController extends AbstractActionController
{
    public function indexAction()
    {
        return $this->redirect()->toRoute('kofus_system', array('controller' => 'translations', 'action' => 'list'));
    }
    
    public function listAction()
    {
        $this->archive()->uriStack()->push();
        $missingTranslations = array();
        foreach ($this->config()->get('locales.available') as $locale) {
            $entities = $this->em()->createQueryBuilder()
                ->select('t')
                ->from('Kofus\System\Entity\TranslationEntity', 't')
                ->where('t.locale = :locale')
                ->setParameter('locale', $locale)
                ->andWhere("t.textDomain <> 'node'")
                ->orderBy('t.id', 'DESC')
                ->getQuery()->getResult();
            
            if ($entities) $missingTranslations[$locale] = $entities;
        }
        
        if ($this->params('id') && in_array($this->params('id'), array_keys($missingTranslations))) {
            $activeLocale = $this->params('id');
        } elseif ($missingTranslations) {
            $activeLocale = array_keys($missingTranslations)[0];
        } else {
            $activeLocale = null;
        }
            
        
        return new ViewModel(array(
            'missingTranslations' => $missingTranslations,
            'activeLocale' => $activeLocale
        ));
    }
    
    public function deleteAction()
    {
        $entity = $this->nodes()->getNode($this->params('id'), 'TL');
        $this->em()->remove($entity);
        $this->em()->flush();
        return $this->redirect()->toRoute('kofus_system', array('controller' => 'translations', 'action' => 'list'));
        
    }
    
    public function deleteOrphansAction()
    {
        $locale = $this->params('id');
        $entities = $this->em()->createQueryBuilder()
            ->select('t')
            ->from('Kofus\System\Entity\TranslationEntity', 't')
            ->where('t.locale = :locale')
            ->setParameter('locale', $locale)
            ->andWhere("t.textDomain <> 'node'")
            ->andWhere('t.value IS NULL')
            ->getQuery()->getResult();
        $count = count($entities);
        foreach ($entities as $entity) {
            $this->em()->remove($entity);
        }
        $this->em()->flush();
        
        $this->flashMessenger()->addSuccessMessage($count . ' Einträge gelöscht');
        return $this->redirect()->toRoute('kofus_system', array('controller' => 'translations', 'action' => 'list', 'id' => $locale));
        
    }
    
    public function deeplAction()
    {
        $locale = $this->params('id');
        $deeplService = $this->getServiceLocator()->get('KofusDeeplService');
        if (in_array($locale, $this->config()->get('locales.available'))) {
            $entities = $this->em()->createQueryBuilder()
                ->select('t')
                ->from('Kofus\System\Entity\TranslationEntity', 't')
                ->where('t.locale = :locale')
                ->setParameter('locale', $locale)
                ->andWhere("t.textDomain <> 'node'")
                ->andWhere('t.value IS NULL')
                ->getQuery()->getResult();
            foreach ($entities as $entity) {
                $deeplService->finishTranslation($entity);
            }
            $this->flashMessenger()->addSuccessMessage(count($entities) . ' Einträge übersetzt');
        } elseif (preg_match('/TL[0-9]+/', $this->params('id'), $matches)) {
            $entity = $this->nodes()->getNode($this->params('id'), 'TL');
            $deeplService->finishTranslation($entity);
            
        } else {
            throw new \Exception('Could not delete ' . $this->params('id'));
        }
        
        return $this->redirect()->toRoute('kofus_system', array('controller' => 'translations', 'action' => 'list', 'id' => $locale));
    }
    
}