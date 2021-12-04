<?php


namespace Villermen\Minecraft\Controller;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Villermen\Minecraft\Service\ViewRenderer;

class DonationController
{
    private const DONATIONS = [
        [
            'name' => 'Rizee',
            'uuid' => '2e31e2e4-5f5c-4a41-aa89-2907ac964d71',
            'date' => '2011-06-08',
            'amount' => '$67.50',
        ],
        [
            'name' => 'Chloronium',
            'uuid' => '4f852fd6-8dcf-44d9-8b24-3ddf6fdc8a27',
            'date' => '2011-06-12',
            'amount' => '$7.50',
        ],
        [
            'name' => 'ThepallexD',
            'uuid' => '972b537e-280d-4c8c-a478-f3a7a4c522db',
            'date' => '2011-06-12',
            'amount' => '$7.50',
        ],
        [
            'name' => 'Crabchicken',
            'uuid' => 'ecd182f4-87de-4832-853b-62d4d52ef3ec',
            'date' => '2011-06-20', // Exact day unknown.
            'amount' => '$14.00',
        ],
        [
            'name' => 'headfirstdiver',
            'uuid' => 'c64cff61-2a56-4acd-8fbc-9bfc91d1f8b1',
            'date' => '2011-06-20', // Exact day unknown.
            'amount' => '$7.50',
        ],
        [
            'name' => 'Knufjes',
            'uuid' => 'eda78af7-3fe8-46d8-82a5-d02740f71f4c',
            'date' => '2011-06-20', // Exact day unknown.
            'amount' => '$7.50',
        ],
        [
            'name' => 'cRiMe2010',
            'uuid' => '6730afdd-ea34-4698-9d0c-ba46e4e9e5c2',
            'date' => '2011-07-01',
            'amount' => '$8.00',
        ],
        [
            'name' => 'BigDaddyQuartet',
            'uuid' => '1668e6d9-a2d8-4a70-b205-c04b08c4554b',
            'date' => '2011-07-01',
            'amount' => '$7.50',
        ],
        [
            'name' => 'slymaster26',
            'uuid' => 'b9991511-e9da-4b94-bdf5-483d70a2c7d6',
            'date' => '2011-08-22',
            'amount' => '$7.50',
        ],
        [
            'name' => 'Faddoo7',
            'uuid' => 'f0eba53e-f3d5-4714-8638-1d1948e48a5e',
            'date' => '2011-08-22',
            'amount' => '$7.50',
        ],
        [
            'name' => 'DonDLow',
            'uuid' => '9ce13721-52d2-47b5-ab1e-40ac643a362e',
            'date' => '2011-08-30',
            'amount' => '$10.00',
        ],
        [
            'name' => 'jordybob',
            'uuid' => '3cc3db00-741e-49e1-a169-f824370caffb',
            'date' => '2011-09-25',
            'amount' => '$7.50',
        ],
        [
            'name' => 'daspan150000',
            'uuid' => '32e5649c-075d-4098-bd11-2fc6e029e3cf',
            'date' => '2011-10-27',
            'amount' => '$7.50',
        ],
        [
            'name' => 'hunter00670',
            'uuid' => '348d2720-09f6-438d-ad3d-b3877fa4e614',
            'date' => '2011-11-03',
            'amount' => '$11.10',
        ],
        [
            'name' => 'verycreepydude',
            'uuid' => '8e98d5da-6736-4bd0-98f9-deadf2fb6b1d',
            'date' => '2011-11-09',
            'amount' => '$30.00',
        ],
        [
            'name' => 'Fixion97',
            'uuid' => 'f99c44a5-f891-4f6f-90da-133b45b91cf2',
            'date' => '2011-11-09',
            'amount' => '$7.50',
        ],
        [
            'name' => 'Kasliah99',
            'uuid' => 'd90c8bca-c9d4-4b3d-8e03-4f82abea5f80',
            'date' => '2011-11-17',
            'amount' => '$7.50',
        ],
        [
            'name' => 'xFireflytex',
            'uuid' => '7af304e8-3199-46f6-bc55-eaf28fad6404',
            'date' => '2011-12-10',
            'amount' => '$7.50',
        ],
        [
            'name' => 'ActiveNoob',
            'uuid' => '87d65b5c-ea75-42b0-ac97-03fd9d6cedbd',
            'date' => '2012-01-04',
            'amount' => '$7.50',
        ],
        [
            'name' => 'candyman106',
            'uuid' => 'fa073f23-945c-4d1d-855f-e045b8bdd4bf',
            'date' => '2012-01-07',
            'amount' => '$7.50',
        ],
        [
            'name' => 'ceduna_kids',
            'uuid' => '5afcb27e-e7a0-41d6-acd8-a217150b983c',
            'date' => '2012-01-08',
            'amount' => '$10.00',
        ],
        [
            'name' => 'mooncoww',
            'uuid' => 'e938b7b3-7012-426b-8c19-e1e7058345e0',
            'date' => '2012-01-17',
            'amount' => '$7.50',
        ],
        [
            'name' => 'daanspeed2',
            'uuid' => '966aa544-4bdc-43c9-8752-9227e2a9f1f0',
            'date' => '2012-02-03',
            'amount' => '$7.50',
        ],
        [
            'name' => 'Scotmai',
            'uuid' => '5cabe932-4d55-48c1-b015-930a1689c828',
            'date' => '2012-02-05',
            'amount' => '$10.00',
        ],
        [
            'name' => 'Revan5159',
            'uuid' => 'e8d60198-7fae-4079-a814-e4d919ef5134',
            'date' => '2012-02-14',
            'amount' => '$10.00',
        ],
        [
            'name' => 'McBaited',
            'uuid' => '3940a231-8f9d-4b77-8901-eb81395a49b0',
            'date' => '2012-02-19',
            'amount' => '$10.00',
        ],
        [
            'name' => 'okden',
            'uuid' => 'c42a5f51-6bdc-4646-bdc5-a1205b79ac2a',
            'date' => '2012-03-05',
            'amount' => '$20.00',
        ],
        [
            'name' => 'xXFlubbyDubbyXx',
            'uuid' => '68a9d8a3-750d-44ef-8b29-5c3f4ff5451d',
            'date' => '2012-03-21',
            'amount' => '$7.50',
        ],
        [
            'name' => 'axlewoof5',
            'uuid' => '42352b8a-73d7-4f71-913c-73f83b548176',
            'date' => '2012-05-11',
            'amount' => '$7.50',
        ],
        [
            'name' => 'Avalition',
            'uuid' => '85eff3df-2221-4afa-9f08-0f85677df757',
            'date' => '2012-05-12',
            'amount' => '$7.50',
        ],
        [
            'name' => 'Readluke',
            'uuid' => '0dfa9e68-e530-46e1-b9c7-a6a4ec4563f2',
            'date' => '2012-05-24',
            'amount' => '$15.00',
        ],
        [
            'name' => 'GeNeTiCShaDoW',
            'uuid' => '3892b767-8b5d-426d-977a-53533dcc733b',
            'date' => '2012-06-05',
            'amount' => '$15.00',
        ],
        [
            'name' => 'BlueMesa',
            'uuid' => '327f7f4a-15cf-4bc2-8d0d-21fb15ad6d65',
            'date' => '2012-06-07',
            'amount' => '$7.50',
        ],
        [
            'name' => 'Lydeckers',
            'uuid' => 'f1a714fc-23c7-4fd7-a519-24aeaee4fdde',
            'date' => '2012-06-22',
            'amount' => '$8.00',
        ],
        [
            'name' => 'McmmoDan',
            'uuid' => 'de7716d6-5b1c-4a4e-8f43-b8363b3f94cd',
            'date' => '2012-06-25',
            'amount' => '$7.50',
        ],
        [
            'name' => 'conthegamer',
            'uuid' => 'ec0fd52c-3b3f-40e3-9830-9bbf790cd1c5',
            'date' => '2012-06-26',
            'amount' => '$7.50',
        ],
        [
            'name' => 'Moodicow',
            'uuid' => '2a5774f1-c7fe-4bb0-ae31-9a4efb925116',
            'date' => '2012-06-28',
            'amount' => '$7.50',
        ],
        [
            'name' => 'notsewz123',
            'uuid' => '591327ce-9e8a-4fc6-b0fa-c9293b132e73',
            'date' => '2012-07-07',
            'amount' => '$15.00',
        ],
        [
            'name' => 'citizen_zane',
            'uuid' => '2fecd5ce-8c05-4837-bc42-628d9694766c',
            'date' => '2012-07-07',
            'amount' => '$10.00',
        ],
        [
            'name' => 'Danton47',
            'uuid' => 'cd538a26-0315-401c-9999-9b561f17c741',
            'date' => '2012-07-17',
            'amount' => '$17.50',
        ],
        [
            'name' => 'creeperdefense',
            'uuid' => '3a82282b-c9f3-45c3-944f-21d7a88c5478',
            'date' => '2012-07-22',
            'amount' => '$7.50',
        ],
        [
            'name' => 'grahamegarrison',
            'uuid' => 'e7d49724-971f-4ceb-bb75-e2c379ee1543',
            'date' => '2012-07-24',
            'amount' => '$7.50',
        ],
        [
            'name' => 'lilygarrison',
            'uuid' => 'd205d684-9cd7-497f-8b15-cf731e885e91',
            'date' => '2012-07-26',
            'amount' => '$7.50',
        ],
        [
            'name' => 'gabers628',
            'uuid' => '86b05bac-64fd-4fa3-8e45-1303035caae9',
            'date' => '2012-07-26',
            'amount' => '$7.50',
        ],
        [
            'name' => 'Boskobom',
            'uuid' => '160a18d6-58f2-415f-897c-c9da2eeba6df',
            'date' => '2012-08-08',
            'amount' => '$7.50',
        ],
        [
            'name' => 'SailorBeeb',
            'uuid' => '48657d09-83dd-4f69-8f53-8738303b31d1',
            'date' => '2012-08-15',
            'amount' => '$15.00',
        ],
        [
            'name' => 'leleroy22',
            'uuid' => '5bac8dc1-43f1-4dd2-9627-7bb473eb4def',
            'date' => '2012-08-18',
            'amount' => '$7.50',
        ],
        [
            'name' => 'Neepsy',
            'uuid' => '1c8f9a3b-5562-40ad-9efe-4510322e0b4c',
            'date' => '2012-08-20',
            'amount' => '$7.50',
        ],
        [
            'name' => 'MiiniMan123',
            'uuid' => 'b0299627-e894-4b27-8371-6839f637abe1',
            'date' => '2012-08-29',
            'amount' => '$7.50',
        ],
        [
            'name' => 'Mr_King21',
            'uuid' => 'a88e1d5c-0207-4242-b5dc-657be33523a9',
            'date' => '2012-08-29',
            'amount' => '$15.00',
        ],
        [
            'name' => 'flybye3',
            'uuid' => null,
            'date' => '2021-09-01',
            'amount' => '$7.50',
        ],
        [
            'name' => 'pookyboo34',
            'uuid' => '75957b13-02c2-41e5-b3a9-b86f115e79d9',
            'date' => '2012-09-09',
            'amount' => '$7.50',
        ],
        [
            'name' => 'Sen66',
            'uuid' => 'e73bc873-a97a-44e7-a8e8-549cc24ad80e',
            'date' => '2012-09-23',
            'amount' => '$20.15',
        ],
        [
            'name' => 'iiDead',
            'uuid' => '1bd832f6-14a9-4b29-9669-06900f3723a7',
            'date' => '2012-09-29',
            'amount' => '$7.50',
        ],
        [
            'name' => 'Luke0328',
            'uuid' => '828c47b4-3a6f-454a-a88f-3f6091e792d4',
            'date' => '2012-10-04',
            'amount' => '$7.50',
        ],
        [
            'name' => 'rainbowlazer',
            'uuid' => '833b2b9c-d9ff-4ab9-a1ba-c2ff5c4d914c',
            'date' => '2012-10-21',
            'amount' => '$12.00',
        ],
        [
            'name' => 'killer11275',
            'uuid' => '360ecbfc-3ef1-43cf-960d-be9e4d361b89',
            'date' => '2012-11-20',
            'amount' => '$8.00',
        ],
        [
            'name' => 'Grant228',
            'uuid' => 'c03462e4-922b-42f1-8de1-aa7c5d5f5e07',
            'date' => '2012-11-22',
            'amount' => '$10.00',
        ],
        [
            'name' => 'EliasRoss',
            'uuid' => '2d3f16d7-ce43-4094-a85f-afe60726353a',
            'date' => '2012-11-24',
            'amount' => '$8.00',
        ],
        [
            'name' => 'ironhorse743912',
            'uuid' => '8d78bd59-ac60-4f60-9400-b089c66f43cd',
            'date' => '2012-12-27',
            'amount' => '$10.00',
        ],
        [
            'name' => 'zombot50',
            'uuid' => '22ac23c2-ee1c-4b8d-8e0a-8bc5628ed358',
            'date' => '2012-12-28',
            'amount' => '$9.00',
        ],
        [
            'name' => 'zikky2',
            'uuid' => 'c91f12b7-8819-481a-98e9-43e6cc032de6',
            'date' => '2012-12-29',
            'amount' => '$8.00',
        ],
        [
            'name' => 'Violet_Q',
            'uuid' => '5e748744-e4ee-40a5-94e8-fdf0a887597a',
            'date' => '2013-01-21',
            'amount' => '$8.00',
        ],
        [
            'name' => 'bigsmile9',
            'uuid' => 'ade38270-646c-4036-b780-5ebd35bcbee0',
            'date' => '2013-01-21',
            'amount' => '$8.00',
        ],
        [
            'name' => 'Ollie____',
            'uuid' => null,
            'date' => '2013-04-08',
            'amount' => '$8.00',
        ],
        [
            'name' => 'koolboy2001',
            'uuid' => '4dba456b-300e-459c-9bef-d335b30e73c4',
            'date' => '2013-04-19',
            'amount' => '$10.00',
        ],
        [
            'name' => 'xBLOODxBROTHERx2',
            'uuid' => 'c5becdee-b46f-417d-b54c-4a0eb89290dc',
            'date' => '2013-06-07',
            'amount' => '$10.00',
        ],
        [
            'name' => 'D_miner_888',
            'uuid' => '2db3f22b-29a9-4279-b20f-42edfd266fb7',
            'date' => '2013-06-12',
            'amount' => '$8.00',
        ],
        [
            'name' => 'RYAN0916',
            'uuid' => null,
            'date' => '2013-06-15',
            'amount' => '$8.00',
        ],
        [
            'name' => 'Smitty2019',
            'uuid' => '89dd6e6f-c71e-405f-b121-39971efe52d8',
            'date' => '2013-06-16',
            'amount' => '$8.00',
        ],
        [
            'name' => 'ScottfaceGamer',
            'uuid' => '1b3c00d6-de8c-4c47-87d5-bd1bdd3d4800',
            'date' => '2013-06-25',
            'amount' => '$8.00',
        ],
        [
            'name' => 'christiangg17',
            'uuid' => '6ec72f68-59b7-4534-ba18-9896ae49cc68',
            'date' => '2013-06-29',
            'amount' => '$8.00',
        ],
        [
            'name' => 'Alien_4ce',
            'uuid' => 'c4aa4390-a2f8-4f07-b86e-4d2d2d8a00e7',
            'date' => '2013-07-07',
            'amount' => '$8.00',
        ],
        [
            'name' => 'J_Smooth27',
            'uuid' => 'c02d0e9f-d41b-4b7a-8db8-93f935170ff3',
            'date' => '2013-07-21',
            'amount' => '$8.00',
        ],
        [
            'name' => 'Arkamedis',
            'uuid' => '596fe5ed-c889-40e0-a40d-7aab44306b58',
            'date' => '2013-07-27',
            'amount' => '$9.00',
        ],
        [
            'name' => 'Onmate',
            'uuid' => '91383568-c2e6-4cf5-a7ba-08528d5b88a4',
            'date' => '2013-07-28',
            'amount' => '$53.50',
        ],
        [
            'name' => 'camww',
            'uuid' => 'a22c6d67-95cb-4a3a-8583-670ee09c2318',
            'date' => '2013-07-30',
            'amount' => '$10.00',
        ],
        [
            'name' => 'Gooby2012',
            'uuid' => null,
            'date' => '2013-07-31',
            'amount' => '$8.00',
        ],
        [
            'name' => 'MegaLazuli',
            'uuid' => '736b251a-7466-47f7-b96d-d437cbe586ef',
            'date' => '2013-08-06',
            'amount' => '$8.00',
        ],
        [
            'name' => 'Lary_Jr',
            'uuid' => '0835aa0d-a907-4029-8f9e-3d015c7a53fe',
            'date' => '2013-08-08',
            'amount' => '$8.00',
        ],
        [
            'name' => 'hugonius',
            'uuid' => '0c0d7aa4-fec3-4a25-847a-bfc6e25da4a7',
            'date' => '2013-08-27',
            'amount' => '$8.00',
        ],
        [
            'name' => 'chilapox',
            'uuid' => '9a3706bb-ba83-44c7-bd7f-bc4ffac64a35',
            'date' => '2013-08-27',
            'amount' => '$10.00',
        ],
        [
            'name' => 'AnimationsRS',
            'uuid' => 'f3c09ccb-f423-4f14-8c73-f1f63321f56d',
            'date' => '2013-09-01',
            'amount' => '$7.50',
        ],
        [
            'name' => 'Dr_PureMix',
            'uuid' => '3f5fc0d3-cd67-48d9-91be-d4fc3be03a2e',
            'date' => '2013-09-02',
            'amount' => '$7.50',
        ],
        [
            'name' => 'Nubungee',
            'uuid' => '8ff62bed-b5ef-4368-96ca-353b511a5654',
            'date' => '2013-09-08',
            'amount' => '$23.00',
        ],
        [
            'name' => 'SeaBlueApples',
            'uuid' => 'c7629302-7885-4ecd-b0d2-be5c58518585',
            'date' => '2013-10-27',
            'amount' => '$8.00',
        ],
        [
            'name' => 'moschiemonster',
            'uuid' => 'c6da8335-af85-4c28-a7cb-4d9df6139f59',
            'date' => '2013-11-02',
            'amount' => '$10.00',
        ],
        [
            'name' => 'Jhenni',
            'uuid' => '1caae36b-fd8d-42e0-9a73-3fdd22eb7be0',
            'date' => '2013-12-26',
            'amount' => '$15.00',
        ],
        [
            'name' => 'Alexzander40',
            'uuid' => '162e8c52-463e-4960-9343-d743c2c78509',
            'date' => '2013-12-29',
            'amount' => '$15.00',
        ],
        [
            'name' => 'Jasmine2000',
            'uuid' => 'b7f37bbe-eae7-46e5-ade9-576e2fa9c900',
            'date' => '2014-02-02',
            'amount' => '$30.00',
        ],
        [
            'name' => 'Cosmic_love',
            'uuid' => 'e5faf843-2f56-4e1c-9f15-bfe0241be105',
            'date' => '2014-02-02',
            'amount' => '$10.00',
        ],
        [
            'name' => 'RainbowMellow',
            'uuid' => '9e9c0068-6a3e-4f29-b9d7-c4bd6ac5cc08',
            'date' => '2014-02-10',
            'amount' => '$8.00',
        ],
        [
            'name' => 'joehobo21',
            'uuid' => '1c9b41d2-af8b-4ff2-9ac0-bb843b839de5',
            'date' => '2014-02-12',
            'amount' => '$8.00',
        ],
        [
            'name' => 'apexminer_',
            'uuid' => '1ba868e1-c469-4117-b041-dfd7be24960c',
            'date' => '2014-04-05',
            'amount' => '$28.00',
        ],
        [
            'name' => 'robonomad',
            'uuid' => '055e9a2c-5241-4aa9-9bc5-372274664ad7',
            'date' => '2014-05-02',
            'amount' => '$28.00',
        ],
        [
            'name' => 'xHybridMiner',
            'uuid' => '2307b99e-3339-4905-afb8-957d225f2aa4',
            'date' => '2014-05-03',
            'amount' => '$20.00',
        ],
        [
            'name' => 'Pockkii',
            'uuid' => 'b03f91b3-98bb-4bee-85f3-ad111994ed14',
            'date' => '2014-05-04',
            'amount' => '$8.00',
        ],
        [
            'name' => 'markosxa',
            'uuid' => '57c74581-fdb8-4019-af2f-6125a91c9221',
            'date' => '2014-05-11',
            'amount' => '$8.00',
        ],
        [
            'name' => 'saviosaltamontes',
            'uuid' => 'dc7e7cad-6d89-4e29-bf08-e0d3f095d567',
            'date' => '2014-07-15',
            'amount' => '$10.00',
        ],
        [
            'name' => 'Flaming_Robot',
            'uuid' => null,
            'date' => '2014-08-01',
            'amount' => '$20.00',
        ],
        [
            'name' => 'mothesnail',
            'uuid' => 'a24342ff-6c6b-4d3d-b420-bc70206e8332',
            'date' => '2014-08-11',
            'amount' => '$15.00',
        ],
        [
            'name' => 'Drew1011',
            'uuid' => '9289d67c-8485-4a0d-bc5a-a19d6b62b9cd',
            'date' => '2014-10-08',
            'amount' => '$8.00',
        ],
        [
            'name' => 'Quizn0',
            'uuid' => '06fcd9f7-3c85-4e1e-9d4d-46c165a57546',
            'date' => '2014-10-09',
            'amount' => '$8.00',
        ],
        [
            'name' => 'Techdog500',
            'uuid' => '609ef100-daef-4b3d-98aa-cd2344b8e382',
            'date' => '2014-12-14',
            'amount' => '$8.00',
        ],
        [
            'name' => 'Sniicker__Z',
            'uuid' => '4a0d5ca4-529b-419e-a971-984dc2ec0161',
            'date' => '2014-12-22',
            'amount' => '$10.00',
        ],
        [
            'name' => 'waterfoxtheelf',
            'uuid' => '97edb82c-4ab9-417a-9268-ea034d6e53ee',
            'date' => '2014-12-22',
            'amount' => '$5.00',
        ],
        [
            'name' => 'DeeganRT',
            'uuid' => '9c22bdec-8c8e-45a7-bc27-4be1f309fe1e',
            'date' => '2015-01-03',
            'amount' => '$8.00',
        ],
        [
            'name' => 'ApexMinerFTW',
            'uuid' => '21a117de-2c7a-4407-a2de-2a9a15ec7748',
            'date' => '2015-01-10',
            'amount' => '$15.25',
        ],
        [
            'name' => 'Sckorponok1',
            'uuid' => '34892651-09e0-44ce-8d65-ee4747c49a8b',
            'date' => '2015-03-21',
            'amount' => '$16.00',
        ],
        [
            'name' => 'Mr_illicit6266',
            'uuid' => '8c9e91ec-be0a-42da-9505-9ca21b12ec7f',
            'date' => '2016-01-16',
            'amount' => '$10.00',
        ],
        [
            'name' => 'fossie2000',
            'uuid' => null,
            'date' => '2016-08-18',
            'amount' => '$15.00',
        ],
        [
            'name' => 'PunishedAnubis',
            'uuid' => 'e0b816bd-366e-43d2-9bb5-521879b410c2',
            'date' => '2020-07-10',
            'amount' => '$5.00',
        ],
        [
            'name' => 'Exij',
            'uuid' => 'f22b2b0e-28bb-4556-bf88-d9482fae7adb',
            'date' => '2020-12-21',
            'amount' => '$15.00',
        ],
        [
            'name' => 'Krojo',
            'uuid' => '665662cd-a81c-48f7-bdad-ea5447aa1f5c',
            'date' => '2020-12-23',
            'amount' => '$15.00',
        ],
        [
            'name' => 'Rootner1007',
            'uuid' => 'dd17d56b-05a1-4e97-8a48-f6764bdccc7e',
            'date' => '2021-01-07',
            'amount' => '$15.00',
        ],
        [
            'name' => 'Frivybela',
            'uuid' => 'adb9e448-ce32-48ae-b32f-276e90cc9090',
            'date' => '2021-02-16',
            'amount' => '$15.00',
        ],
        [
            'name' => 'Breckin37',
            'uuid' => '230a63f9-2155-48a1-bac6-17a1d4a6bc12',
            'date' => '2021-02-28',
            'amount' => '$9.50',
        ],
        [
            'name' => 'CubicCreations',
            'uuid' => null,
            'date' => null,
            'amount' => '$30.00',
        ],
    ];

    private ViewRenderer $viewRenderer;

    public function __construct(ViewRenderer $viewRenderer)
    {
        $this->viewRenderer = $viewRenderer;
    }

    public function donatingAction(Request $request, Response $response): void
    {
        $response->setContent($this->viewRenderer->renderView('page/donating.html.twig', [
            'donations' => self::DONATIONS,
        ]));
    }
}
