# Glasgow Council Bin Collection to MQTT

Single purpose service to take the next cycle of bin collections and publish them to MQTT for consumption in Home Assistant.

https://www.glasgow.gov.uk/forms/refuseandrecyclingcalendar/AddressSearch.aspx

## Requirements

- Ability to run a container
- MQTT Server & Your Glasgow Council Bin ID (UPRN), you can grab this from the online form in the URL

## Basic Container Workflow

```bash
docker run -it --name glasgow-bin-collection --rm \
 -e MQTT_HOST=<ip>> \
 -e GLASGOW_COUNCIL_ID=<id>> \
 gsdevme/glasgow-bin-collection-mqtt:1.0.0
```

## Home Assistant

For each coloured bin create a `input_datetime` and `automation` like the following

```yaml
# configuration.yaml
input_datetime:
    blue_bin:
        name: blue_bin
        has_date: true
        has_time: false
```

```yaml
# automations.yaml
- alias: 'Set blue bin date'
  trigger:
    platform: mqtt
    topic: "gcc/bins/blue"
  condition: []
  action:
    - service: input_datetime.set_datetime
      data_template:
        entity_id: input_datetime.blue_bin
        date: "{{ trigger.payload_json.date }}"
```
