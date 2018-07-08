require 'json'

url = "https://raw.githubusercontent.com/driftyco/ionicons/master/builder/build_data.json"

json = `curl -s #{url}`
json = JSON.parse(json)

icons = {}

json[ 'icons' ].each do |item|
	icon = item['name']

	icons[icon] = {
		label: icon,
		code: item['code']
	}
end

File.write(File.expand_path(File.join(File.dirname(__FILE__), '..') + '/astoundify-themecustomizer/AssetSources/Ionicons/icons.json'), JSON.pretty_generate(icons))

puts "Updated astoundify-themecustomizer/AssetSources/Ionicons/icons.json"
