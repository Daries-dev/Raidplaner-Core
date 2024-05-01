<?php

/**
 * @author  Marco Daries
 * @copyright   2023-2024 Daries.dev
 * @license Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */

use wcf\system\database\table\column\DefaultFalseBooleanDatabaseTableColumn;
use wcf\system\database\table\column\IntDatabaseTableColumn;
use wcf\system\database\table\column\MediumtextDatabaseTableColumn;
use wcf\system\database\table\column\NotNullInt10DatabaseTableColumn;
use wcf\system\database\table\column\NotNullVarchar191DatabaseTableColumn;
use wcf\system\database\table\column\NotNullVarchar255DatabaseTableColumn;
use wcf\system\database\table\column\ObjectIdDatabaseTableColumn;
use wcf\system\database\table\column\SmallintDatabaseTableColumn;
use wcf\system\database\table\column\TextDatabaseTableColumn;
use wcf\system\database\table\column\VarcharDatabaseTableColumn;
use wcf\system\database\table\DatabaseTable;
use wcf\system\database\table\index\DatabaseTableForeignKey;
use wcf\system\database\table\index\DatabaseTableIndex;
use wcf\system\database\table\index\DatabaseTablePrimaryIndex;
use wcf\system\database\table\PartialDatabaseTable;

return [
    DatabaseTable::create('rp1_classification')
        ->columns([
            ObjectIdDatabaseTableColumn::create('classificationID'),
            NotNullInt10DatabaseTableColumn::create('gameID'),
            NotNullVarchar255DatabaseTableColumn::create('identifier'),
            NotNullVarchar255DatabaseTableColumn::create('title'),
            NotNullVarchar255DatabaseTableColumn::create('icon')
                ->defaultValue(''),
            DefaultFalseBooleanDatabaseTableColumn::create('isDisabled'),
            NotNullInt10DatabaseTableColumn::create('packageID'),
        ])
        ->indices([
            DatabaseTablePrimaryIndex::create()
                ->columns(['classificationID']),
            DatabaseTableIndex::create('identifier_gameID')
                ->columns(['identifier', 'gameID'])
                ->type(DatabaseTableIndex::UNIQUE_TYPE),
        ])
        ->foreignKeys([
            DatabaseTableForeignKey::create()
                ->columns(['packageID'])
                ->referencedTable('wcf1_package')
                ->referencedColumns(['packageID'])
                ->onDelete('CASCADE'),
        ]),

    DatabaseTable::create('rp1_classification_to_faction')
        ->columns([
            NotNullInt10DatabaseTableColumn::create('classificationID'),
            NotNullInt10DatabaseTableColumn::create('factionID'),
        ])
        ->indices([
            DatabaseTableIndex::create('classificationID_factionID')
                ->columns(['classificationID', 'factionID'])
                ->type(DatabaseTableIndex::UNIQUE_TYPE),
        ])
        ->foreignKeys([
            DatabaseTableForeignKey::create()
                ->columns(['classificationID'])
                ->referencedTable('rp1_classification')
                ->referencedColumns(['classificationID'])
                ->onDelete('CASCADE'),
        ]),

    DatabaseTable::create('rp1_classification_to_race')
        ->columns([
            NotNullInt10DatabaseTableColumn::create('classificationID'),
            NotNullInt10DatabaseTableColumn::create('raceID'),
        ])
        ->indices([
            DatabaseTableIndex::create('classificationID_raceID')
                ->columns(['classificationID', 'raceID'])
                ->type(DatabaseTableIndex::UNIQUE_TYPE),
        ])
        ->foreignKeys([
            DatabaseTableForeignKey::create()
                ->columns(['classificationID'])
                ->referencedTable('rp1_classification')
                ->referencedColumns(['classificationID'])
                ->onDelete('CASCADE'),
        ]),

    DatabaseTable::create('rp1_classification_to_role')
        ->columns([
            NotNullInt10DatabaseTableColumn::create('classificationID'),
            NotNullInt10DatabaseTableColumn::create('roleID'),
        ])
        ->indices([
            DatabaseTableIndex::create('classificationID_roleID')
                ->columns(['classificationID', 'roleID'])
                ->type(DatabaseTableIndex::UNIQUE_TYPE),
        ])
        ->foreignKeys([
            DatabaseTableForeignKey::create()
                ->columns(['classificationID'])
                ->referencedTable('rp1_classification')
                ->referencedColumns(['classificationID'])
                ->onDelete('CASCADE'),
        ]),

    DatabaseTable::create('rp1_faction')
        ->columns([
            ObjectIdDatabaseTableColumn::create('factionID'),
            NotNullInt10DatabaseTableColumn::create('gameID'),
            NotNullVarchar255DatabaseTableColumn::create('identifier'),
            NotNullVarchar255DatabaseTableColumn::create('title'),
            NotNullVarchar255DatabaseTableColumn::create('icon')
                ->defaultValue(''),
            DefaultFalseBooleanDatabaseTableColumn::create('isDisabled'),
            NotNullInt10DatabaseTableColumn::create('packageID'),
        ])
        ->indices([
            DatabaseTablePrimaryIndex::create()
                ->columns(['factionID']),
            DatabaseTableIndex::create('identifier_gameID')
                ->columns(['identifier', 'gameID'])
                ->type(DatabaseTableIndex::UNIQUE_TYPE),
        ])
        ->foreignKeys([
            DatabaseTableForeignKey::create()
                ->columns(['packageID'])
                ->referencedTable('wcf1_package')
                ->referencedColumns(['packageID'])
                ->onDelete('CASCADE'),
        ]),

    PartialDatabaseTable::create('rp1_classification_to_faction')
        ->foreignKeys([
            DatabaseTableForeignKey::create()
                ->columns(['factionID'])
                ->referencedTable('rp1_faction')
                ->referencedColumns(['factionID'])
                ->onDelete('CASCADE'),
        ]),

    DatabaseTable::create('rp1_game')
        ->columns([
            ObjectIdDatabaseTableColumn::create('gameID'),
            NotNullVarchar255DatabaseTableColumn::create('identifier'),
            NotNullVarchar255DatabaseTableColumn::create('title'),
            NotNullInt10DatabaseTableColumn::create('packageID'),
        ])
        ->indices([
            DatabaseTablePrimaryIndex::create()
                ->columns(['gameID']),
            DatabaseTableIndex::create('identifier')
                ->columns(['identifier'])
                ->type(DatabaseTableIndex::UNIQUE_TYPE),
        ])
        ->foreignKeys([
            DatabaseTableForeignKey::create()
                ->columns(['packageID'])
                ->referencedTable('wcf1_package')
                ->referencedColumns(['packageID'])
                ->onDelete('CASCADE'),
        ]),

    PartialDatabaseTable::create('rp1_classification')
        ->foreignKeys([
            DatabaseTableForeignKey::create()
                ->columns(['gameID'])
                ->referencedTable('rp1_game')
                ->referencedColumns(['gameID'])
                ->onDelete('CASCADE'),
        ]),

    PartialDatabaseTable::create('rp1_faction')
        ->foreignKeys([
            DatabaseTableForeignKey::create()
                ->columns(['gameID'])
                ->referencedTable('rp1_game')
                ->referencedColumns(['gameID'])
                ->onDelete('CASCADE'),
        ]),

    DatabaseTable::create('rp1_member')
        ->columns([
            ObjectIdDatabaseTableColumn::create('characterID'),
            VarcharDatabaseTableColumn::create('characterName')
                ->length(100)
                ->notNull(),
            IntDatabaseTableColumn::create('userID')
                ->length(10),
            NotNullInt10DatabaseTableColumn::create('gameID'),
            IntDatabaseTableColumn::create('avatarID')
                ->length(10),
            NotNullInt10DatabaseTableColumn::create('created')
                ->defaultValue(0),
            NotNullInt10DatabaseTableColumn::create('lastUpdateTime')
                ->defaultValue(0),
            MediumtextDatabaseTableColumn::create('notes'),
            TextDatabaseTableColumn::create('additionalData'),
            VarcharDatabaseTableColumn::create('guildName')
                ->length(100)
                ->notNull()
                ->defaultValue(''),
            NotNullInt10DatabaseTableColumn::create('views')
                ->defaultValue(0),
            DefaultFalseBooleanDatabaseTableColumn::create('isPrimary'),
            DefaultFalseBooleanDatabaseTableColumn::create('isDisabled'),
        ])
        ->indices([
            DatabaseTablePrimaryIndex::create()
                ->columns(['characterID']),
            DatabaseTableIndex::create('characterName_gameID')
                ->columns(['characterName', 'gameID'])
                ->type(DatabaseTableIndex::UNIQUE_TYPE),
        ])
        ->foreignKeys([
            DatabaseTableForeignKey::create()
                ->columns(['userID'])
                ->referencedTable('wcf1_user')
                ->referencedColumns(['userID'])
                ->onDelete('SET NULL'),
            DatabaseTableForeignKey::create()
                ->columns(['gameID'])
                ->referencedTable('rp1_game')
                ->referencedColumns(['gameID'])
                ->onDelete('CASCADE'),
        ]),

    DatabaseTable::create('rp1_member_avatar')
        ->columns([
            ObjectIdDatabaseTableColumn::create('avatarID'),
            NotNullVarchar255DatabaseTableColumn::create('avatarName')
                ->defaultValue(''),
            VarcharDatabaseTableColumn::create('avatarExtension')
                ->length(7)
                ->notNull()
                ->defaultValue(''),
            SmallintDatabaseTableColumn::create('width')
                ->length(5)
                ->notNull()
                ->defaultValue(0),
            SmallintDatabaseTableColumn::create('height')
                ->length(5)
                ->notNull()
                ->defaultValue(0),
            IntDatabaseTableColumn::create('characterID')
                ->length(10),
            VarcharDatabaseTableColumn::create('fileHash')
                ->length(40)
                ->notNull()
                ->defaultValue(''),
            DefaultFalseBooleanDatabaseTableColumn::create('hasWebP'),
        ])
        ->indices([
            DatabaseTablePrimaryIndex::create()
                ->columns(['avatarID']),
        ])
        ->foreignKeys([
            DatabaseTableForeignKey::create()
                ->columns(['characterID'])
                ->referencedTable('rp1_member')
                ->referencedColumns(['characterID'])
                ->onDelete('CASCADE'),
        ]),

    PartialDatabaseTable::create('rp1_member')
        ->foreignKeys([
            DatabaseTableForeignKey::create()
                ->columns(['avatarID'])
                ->referencedTable('rp1_member_avatar')
                ->referencedColumns(['avatarID'])
                ->onDelete('SET NULL'),
        ]),


    DatabaseTable::create('rp1_race')
        ->columns([
            ObjectIdDatabaseTableColumn::create('raceID'),
            NotNullInt10DatabaseTableColumn::create('gameID'),
            NotNullVarchar255DatabaseTableColumn::create('identifier'),
            NotNullVarchar255DatabaseTableColumn::create('title'),
            NotNullVarchar255DatabaseTableColumn::create('icon')
                ->defaultValue(''),
            DefaultFalseBooleanDatabaseTableColumn::create('isDisabled'),
            NotNullInt10DatabaseTableColumn::create('packageID'),
        ])
        ->indices([
            DatabaseTablePrimaryIndex::create()
                ->columns(['raceID']),
            DatabaseTableIndex::create('identifier_gameID')
                ->columns(['identifier', 'gameID'])
                ->type(DatabaseTableIndex::UNIQUE_TYPE),
        ])
        ->foreignKeys([
            DatabaseTableForeignKey::create()
                ->columns(['gameID'])
                ->referencedTable('rp1_game')
                ->referencedColumns(['gameID'])
                ->onDelete('CASCADE'),
            DatabaseTableForeignKey::create()
                ->columns(['packageID'])
                ->referencedTable('wcf1_package')
                ->referencedColumns(['packageID'])
                ->onDelete('CASCADE'),
        ]),

    PartialDatabaseTable::create('rp1_classification_to_race')
        ->foreignKeys([
            DatabaseTableForeignKey::create()
                ->columns(['raceID'])
                ->referencedTable('rp1_race')
                ->referencedColumns(['raceID'])
                ->onDelete('CASCADE'),
        ]),

    DatabaseTable::create('rp1_race_to_faction')
        ->columns([
            NotNullInt10DatabaseTableColumn::create('raceID'),
            NotNullInt10DatabaseTableColumn::create('factionID'),
        ])
        ->indices([
            DatabaseTableIndex::create('raceID_factionID')
                ->columns(['raceID', 'factionID'])
                ->type(DatabaseTableIndex::UNIQUE_TYPE),
        ])
        ->foreignKeys([
            DatabaseTableForeignKey::create()
                ->columns(['raceID'])
                ->referencedTable('rp1_race')
                ->referencedColumns(['raceID'])
                ->onDelete('CASCADE'),
            DatabaseTableForeignKey::create()
                ->columns(['factionID'])
                ->referencedTable('rp1_faction')
                ->referencedColumns(['factionID'])
                ->onDelete('CASCADE'),
        ]),

    DatabaseTable::create('rp1_role')
        ->columns([
            ObjectIdDatabaseTableColumn::create('roleID'),
            NotNullInt10DatabaseTableColumn::create('gameID'),
            NotNullVarchar255DatabaseTableColumn::create('identifier'),
            NotNullVarchar255DatabaseTableColumn::create('title'),
            NotNullVarchar255DatabaseTableColumn::create('icon')
                ->defaultValue(''),
            DefaultFalseBooleanDatabaseTableColumn::create('isDisabled'),
            NotNullInt10DatabaseTableColumn::create('packageID'),
        ])
        ->indices([
            DatabaseTablePrimaryIndex::create()
                ->columns(['roleID']),
            DatabaseTableIndex::create('identifier_gameID')
                ->columns(['identifier', 'gameID'])
                ->type(DatabaseTableIndex::UNIQUE_TYPE),
        ])
        ->foreignKeys([
            DatabaseTableForeignKey::create()
                ->columns(['gameID'])
                ->referencedTable('rp1_game')
                ->referencedColumns(['gameID'])
                ->onDelete('CASCADE'),
            DatabaseTableForeignKey::create()
                ->columns(['packageID'])
                ->referencedTable('wcf1_package')
                ->referencedColumns(['packageID'])
                ->onDelete('CASCADE'),
        ]),

    PartialDatabaseTable::create('rp1_classification_to_role')
        ->foreignKeys([
            DatabaseTableForeignKey::create()
                ->columns(['roleID'])
                ->referencedTable('rp1_role')
                ->referencedColumns(['roleID'])
                ->onDelete('CASCADE'),
        ]),

    DatabaseTable::create('rp1_server')
        ->columns([
            ObjectIdDatabaseTableColumn::create('serverID'),
            NotNullInt10DatabaseTableColumn::create('gameID'),
            NotNullVarchar255DatabaseTableColumn::create('identifier'),
            NotNullVarchar255DatabaseTableColumn::create('title'),
            NotNullVarchar255DatabaseTableColumn::create('type')
                ->defaultValue(''),
            NotNullVarchar255DatabaseTableColumn::create('serverGroup')
                ->defaultValue(''),
            NotNullInt10DatabaseTableColumn::create('packageID'),
        ])
        ->indices([
            DatabaseTablePrimaryIndex::create()
                ->columns(['serverID']),
            DatabaseTableIndex::create('identifier_gameID')
                ->columns(['identifier', 'gameID'])
                ->type(DatabaseTableIndex::UNIQUE_TYPE),
        ])
        ->foreignKeys([
            DatabaseTableForeignKey::create()
                ->columns(['gameID'])
                ->referencedTable('rp1_game')
                ->referencedColumns(['gameID'])
                ->onDelete('CASCADE'),
            DatabaseTableForeignKey::create()
                ->columns(['packageID'])
                ->referencedTable('wcf1_package')
                ->referencedColumns(['packageID'])
                ->onDelete('CASCADE'),
        ]),
];
