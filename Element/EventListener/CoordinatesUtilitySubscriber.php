<?php

namespace Mapbender\CoordinatesUtilityBundle\Element\EventListener;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvents;

class CoordinatesUtilitySubscriber implements EventSubscriberInterface
{
    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::PRE_SUBMIT   => 'preSubmit',
        ];
    }

    /**
     * Checkt form fields by PRE_SET_DATA FormEvent
     *
     * @param FormEvent $event
     */
    public function preSetData(FormEvent $event)
    {
        $data = $event->getData();

        if (null === $data || empty($data['srsList'])) {
            return;
        }

        $data['srsList'] = $this->formatSrsOutput($data['srsList']);

        $event->setData($data);
    }

    /**
     * Format SRS list for output
     *
     * @param array $srsListData
     * @return string
     */
    private function formatSrsOutput($srsListData)
    {
        $srsList = array_map(
            function($srs) {
                return $srs['name'] . (!empty($srs['title']) ? ' | ' . $srs['title'] : '');
            },
            $srsListData
        );

        return implode(', ', $srsList);
    }

    /**
     * Checkt form fields by PRE_SUBMIT FormEvent
     *
     * @param FormEvent $event
     */
    public function preSubmit(FormEvent $event)
    {
        $data = $event->getData();

        if (null === $data || empty($data['srsList'])) {
            return;
        }

        $data['srsList'] = $this->retrieveUniqueSRSNamesAndTitles($data['srsList']);

        $event->setData($data);
    }

    /**
     * Retrieve unique SRS names and titles from the input string
     *
     * @param string $srsList
     * @return array
     */
    private function retrieveUniqueSRSNamesAndTitles($srsList)
    {
        $srsArray = explode(",", $srsList);
        $srsToSave = [];

        foreach ($srsArray as $srs) {
            $name = $srs;
            $title = '';

            if (strpos($srs, "|") !== false) {

                $srsParts = explode("|", $srs);

                $name = isset($srsParts[0]) ? $srsParts[0] : '';
                $title = isset($srsParts[1]) ? $srsParts[1] : '';

            }

            $srsToSave[] = [
                "name"  => trim($name),
                "title" => trim($title),
            ];
        }

        return array_unique($srsToSave, SORT_REGULAR);
    }
}
