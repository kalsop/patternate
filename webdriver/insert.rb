#!/usr/bin/env ruby

require 'rubygems'
require 'selenium-webdriver'
require 'mysql'

@browser = Selenium::WebDriver.for :chrome

@con = Mysql.new('127.0.0.1', 'root', 'root', 'patternate-scratch', 8889)

# HERE BE METHODS

def pattern_number
  number = @browser.find_element(:id, 'product_name').text
  return number.gsub("V", "")
  #todo do something to get rid of extra spaces
end

def pattern_collection
  pattern_collection_id = nil
  if !@browser.find_elements(:id, 'brand_image').empty?
    pattern_collection_name = @browser.find_element(:id, 'brand_image').find_element(:tag_name, 'img').attribute('title')
    rs = @con.query("select id from collection where name = '#{pattern_collection_name}'")
    if rs.num_rows == 0
      @con.query("INSERT INTO collection(name, pattern_company_id) VALUES('#{pattern_collection_name}', 1)")
      @con.query("SELECT LAST_INSERT_ID();").each do | row |
        row.each do | row_value |
          pattern_collection_id = row_value
        end
      end
    else
      rs.each do | row |
        row.each do | row_value |
        pattern_collection_id = row_value
        end
      end
    end
  end
  pattern_collection_id
end

def garment_type
  garment_type_string = @browser.find_element(:id, 'product_name_new').text
  matched_words = []
  ["jacket", "dress", "top", "shorts", "pants", "petite", "blouse", "shirt", "tunic", "coat", "cape", "jumpsuit", "skirt", "vest", "robe", "gown", "corset", "maternity", "jeans", "fitting shell"].each do |fancy_word|
    case_insensitive_garment_type = garment_type_string.downcase
    if case_insensitive_garment_type.include? fancy_word
      rs = @con.query("select id from garment_type where lower(name) = '#{fancy_word}'")
      rs.each do |row|
        row.each do | row_value |
          matched_words << row_value
        end
      end
    end
  end
  matched_words
end

def pattern_for
  pattern_for_string = @browser.find_element(:id, 'product_name_new').text.downcase
  {"men" => 1,"misses" => 2}.each_pair do |gender, id |
    return id if pattern_for_string.include? gender
  end
end

def pattern_description
  description = @browser.find_element(:css, '#included_content table tr:first-child td').text
  first, *rest = description.split(/: /)
  rest.join("\s")
end

def pattern_fabric
  @browser.find_elements(:css, '#included_content table td').each do | description_cell |
    if description_cell.text.include? 'FABRICS' 
      return description_cell.text.gsub("FABRICS: ", "")
    end
  end
end

def sizes 
  if @browser.find_element(:css, '.options option').text == "All Sizes in One Envelope "
    sizes_list = ["A", "B", "C", "D", "E", "F", "G", "H", "I", "J"]
  else
    sizes_list = []
    @browser.find_element(:css, '.options').find_elements(:tag_name, 'option').each do | option |
      sizes_list.concat option.text.split('(').last.tr(')A-Za-z ','').split('-')
    end
    sizes_list.uniq
  end
end

def images 
  #images_list = ''
  #@browser.find_elements(:css, '.alt_image_replacement img').each do | image |
  # images_list << image.attribute('src') + ' '
  #end
  #images_list
  #
  @browser.find_element(:css, '#image_original img').attribute('src')
end

def line_art
  @browser.find_element(:css, '#included_content img:first-child').attribute('src')
end

def go_to_search_result_url(start)
  @browser.get "https://www.google.co.uk/search?q=site:voguepatterns.mccall.com+misses&start=#{start}"
end

def pattern_page_url
  @browser.current_url
end

# HERE BE EXECUTION


(3..10).each do |offset|

go_to_search_result_url offset*10

urls = []
@browser.find_elements(:css, 'h3.r a').each do | result |
  urls << result.attribute('href')
end

urls.each do |url|  
  
  begin

  @browser.get url
#http://voguepatterns.mccall.com/v1350-products-46626.php?page_id=1112
#http://voguepatterns.mccall.com/v1174-products-11082.php?page_id=1107
#http://voguepatterns.mccall.com/v1358-products-47535.php?page_id=320
#http://voguepatterns.mccall.com/v1349-products-46625.php?page_id=313
#http://voguepatterns.mccall.com/v1362-products-47539.php?page_id=316
#http://mccallpattern.mccall.com/m6837-products-47784.php?page_id=96
#http://kwiksew.mccall.com/k4026-products-47754.php?page_id=3013
#http://voguepatterns.mccall.com/v8893-products-46634.php?page_id=4444
#http://voguepatterns.mccall.com/v8916-products-46657.php
#http://voguepatterns.mccall.com/v1357-products-46633.php?page_id=862
#http://voguepatterns.mccall.com/v8932-products-47559.php?page_id=174
#http://voguepatterns.mccall.com/v8902-products-46643.php?page_id=4515
#http://voguepatterns.mccall.com/v1084-products-9745.php?page_id=850
#http://voguepatterns.mccall.com/v1304-products-22878.php?page_id=3506
#http://voguepatterns.mccall.com/v1132-products-10466.php?page_id=863
#http://voguepatterns.mccall.com/v1325-products-27098.php?page_id=862
#http://voguepatterns.mccall.com/v8940-products-47567.php?page_id=174
#http://mccallpattern.mccall.com/m6846-products-47793.php?page_id=96
#http://butterick.mccall.com/b5953-products-47646.php?page_id=147
#http://shops.mccall.com/f2704-products-47666.php?page_id=3453
#http://voguepatterns.mccall.com/v1165-products-10765.php?page_id=852

patterns = {:pattern_number => pattern_number, :pattern_collection => pattern_collection, :pattern_description => pattern_description, :pattern_for => pattern_for, :pattern_fabric => pattern_fabric, :garment_type => garment_type, :sizes => sizes, :images => images, :line_art => line_art, :url => pattern_page_url}


query = "INSERT INTO patterns(pattern_company_id, 
                              pattern_number,
                              pattern_collection_id,
                              main_image,
                              line_drawing,
                              description,
                              url) VALUES('1','#{patterns[:pattern_number]}','#{patterns[:pattern_collection]}','#{patterns[:images]}','#{patterns[:line_art]}','#{patterns[:pattern_description]}', '#{patterns[:url]}')"
puts query

query_two = "SELECT LAST_INSERT_ID();"
puts query_two

@con.query(query)
 
rs_last_insert_id = @con.query(query_two)

last_insert_id = ''
rs_last_insert_id.each do |row|
  row.each do |row_value|
    last_insert_id = row_value
  end
end

patterns[:garment_type].each do | garment_type_id |
  query = "INSERT INTO pattern_garment_type(pattern_id,garment_type_id) VALUES ('#{last_insert_id}',#{garment_type_id})"
  puts query
  @con.query(query)
end
query = "INSERT INTO pattern_pattern_for(pattern_id,pattern_for_id) VALUES ('#{last_insert_id}',#{patterns[:pattern_for]})"
puts query
@con.query(query)
    
File.open('/Users/kalsop/Documents/Personal/Dev/webdriver/results', 'a+') do | file |
  patterns.each_value do | value | 
    file.write("#{value} || ")
  end
  file.write "\n\n" 
end
  
rescue Exception => e
  puts "!! ERROR SCRAPING PAGE :"
  puts "URL:"
  puts url
  puts "TITLE"
  puts @browser.title
  puts "MESSAGE:"
  puts e.message
  puts e.backtrace
end
  
end

end




at_exit do
  @con.close
  @browser.quit
end
