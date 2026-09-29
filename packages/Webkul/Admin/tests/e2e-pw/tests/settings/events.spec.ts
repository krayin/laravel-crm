import { env } from "process";
import { test } from "../../fixtures/AdminFixtures";
import { eventdata, EventsPage } from "../../pages/settings/EventsPage";
import { AdminPage } from "../../pages/AdminPage";

test.describe('event management', () => {



  test('create event', async ({ adminPage }) => {
    const event = await new EventsPage(adminPage);
    await event.navigateToEvents();
    await event.createEvent(eventdata);

  })
  test('verify update event', async ({ adminPage }) => {
    const event = await new EventsPage(adminPage);
    await event.navigateToEvents();
    await event.searchByName(eventdata.name);
    await event.editEvents(eventdata);
  })
  test('verify delete event', async ({ adminPage }) => {
    const event = await new EventsPage(adminPage);
    await event.navigateToEvents();
    await event.searchByName(eventdata.name);
    await event.deleteEvent();
  })
})