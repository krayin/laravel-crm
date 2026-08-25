import { test } from "../../fixtures/AdminFixtures";
import { AdminPage } from "../../pages/AdminPage";
import { CampaignData, campaignData, CampaignsPage } from "../../pages/settings/CampaignsPage";
import { eventdata, EventsPage } from "../../pages/settings/EventsPage";



test.describe('campaign management ', () => {
  const updatedCampaignData: CampaignData = {
    name: "updated name",
    event: eventdata,
    subject: "updated subject",
    emailTemplateId: "hslafuieiwfnew",
    active: 1

  }

  test('verify campaing create', async ({ adminPage }) => {
    /*before creating campaign created event for the campaing */
    const event = await new EventsPage(adminPage);
    await event.navigateToEvents();
    await event.createEvent(campaignData.event);
    /*creating campaing after created event*/
    const campaign = new CampaignsPage(adminPage);
    await campaign.navigateToCampaigns();
    await campaign.createCampaign(campaignData);

  })
  test('verify udpate campaign', async ({ adminPage }) => {
    const campaign = new CampaignsPage(adminPage);
    await campaign.navigateToCampaigns();
    await campaign.searchByName(campaignData.name);
    await campaign.editCampaign(campaignData);
  })
  test('verify delete campaign', async ({ adminPage }) => {
    const campaign = new CampaignsPage(adminPage);
    await campaign.navigateToCampaigns();
    await campaign.searchByName(campaignData.name);
    await campaign.deleteCampaign();

  });


})