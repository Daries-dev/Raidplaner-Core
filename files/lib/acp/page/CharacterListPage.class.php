<?php

namespace rp\acp\page;

use rp\data\character\CharacterProfileList;
use wcf\http\Helper;
use wcf\page\SortablePage;
use wcf\system\database\util\PreparedStatementConditionBuilder;
use wcf\system\exception\IllegalLinkException;
use wcf\system\WCF;

/**
 * Shows the result of a character search.
 * 
 * @author  Marco Daries
 * @copyright   2023-2024 Daries.dev
 * @license Free License <https://daries.dev/en/license-for-free-plugins>
 */
final class CharacterListPage extends SortablePage
{
    /**
     * list of character ids
     * @var int[]
     */
    public array $characterIDs = [];

    /**
     * condition builder for character filtering
     */
    public PreparedStatementConditionBuilder $conditions;

    /**
     * @inheritDoc
     */
    public $defaultSortField = 'characterName';

    /**
     * @inheritDoc
     */
    public $itemsPerPage = 50;

    /**
     * @inheritDoc
     */
    public $neededPermissions = ['admin.rp.canSearchCharacter'];

    /**
     * @inheritDoc
     */
    public $objectListClassName = CharacterProfileList::class;

    /**
     * id of a character search
     */
    public int $searchID = 0;

    /**
     * @inheritDoc
     */
    public $validSortFields = [
        'characterID',
        'characterName',
        'created',
        'username',
    ];

    /**
     * @inheritDoc
     */
    public function assignVariables(): void
    {
        parent::assignVariables();

        WCF::getTPL()->assign([
            'searchID' => $this->searchID,
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function initObjectList(): void
    {
        parent::initObjectList();

        $this->conditions->add('member.gameID = ?', [RP_CURRENT_GAME_ID]);

        $this->objectList->sqlSelects = " user.username";
        $this->objectList->sqlJoins .= " LEFT JOIN wcf" . WCF_N . "_user user ON (user.userID = member.userID)";

        $this->objectList->setConditionBuilder($this->conditions);
    }

    /**
     * @inheritDoc
     */
    public function readParameters(): void
    {
        parent::readParameters();

        $this->conditions = new PreparedStatementConditionBuilder();

        try {
            $queryParameters = Helper::mapQueryParameters(
                $_GET,
                <<<'EOT'
                array {
                    id?: positive-int
                }
                EOT
            );
            $this->searchID = $queryParameters['id'] ?? 0;
            if ($this->searchID) {
                $this->readSearchResult();

                if (empty($this->characterIDs)) {
                    throw new IllegalLinkException();
                }

                $this->conditions->add('member.characterID IN (?)', [$this->characterIDs]);
            }
        } catch (MappingError) {
            throw new IllegalLinkException();
        }
    }

    /**
     * Fetches the result of the search with the given search id.
     */
    protected function readSearchResult(): void
    {
        //get character search from database
        $sql = "SELECT  searchData
                FROM    wcf1_search
                WHERE   searchID = ?
                    AND userID = ?
                    AND searchType = ?";
        $statement = WCF::getDB()->prepare($sql);
        $statement->execute([
            $this->searchID,
            WCF::getUser()->userID,
            'characters'
        ]);
        $search = $statement->fetchArray();
        if (!isset($search['searchData'])) {
            throw new IllegalLinkException();
        }

        $data = \unserialize($search['searchData']);
        $this->characterIDs = $data['matches'];
        $this->itemsPerPage = $data['itemsPerPage'];
        unset($data);
    }

    /**
     * @inheritDoc
     */
    public function show(): void
    {
        $this->activeMenuItem = 'rp.acp.menu.link.character.' . ($this->searchID ? 'search' : 'list');

        parent::show();
    }
}
